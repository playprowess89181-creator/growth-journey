<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VCategory;
use App\Models\VocabularyWord;
use App\Models\VocabularyWordCompletion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VocabularyController extends Controller
{
    public function categories(Request $request): JsonResponse
    {
        $supported = $this->supportedLanguageCodes();

        $payload = [
            'lang' => strtolower(trim((string) $request->query('lang'))),
        ];

        $validator = Validator::make($payload, [
            'lang' => ['required', 'string', 'max:10', Rule::in($supported)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $lang = strtolower(trim((string) $validator->validated()['lang']));

        $user = $request->user();
        $completedByCategory = collect();
        if ($user) {
            $completedByCategory = DB::table('vocabulary_word_completions')
                ->join('vocabulary_words', 'vocabulary_word_completions.word_id', '=', 'vocabulary_words.id')
                ->where('vocabulary_word_completions.user_id', $user->id)
                ->select([
                    'vocabulary_words.category_id',
                    DB::raw('COUNT(*) as completed_words_count'),
                ])
                ->groupBy('vocabulary_words.category_id')
                ->pluck('completed_words_count', 'vocabulary_words.category_id');
        }

        $categories = VCategory::query()
            ->where('is_active', true)
            ->with([
                'translations' => function ($q) use ($lang) {
                    $q->whereIn('language_code', [$lang, 'en']);
                },
            ])
            ->withCount('words')
            ->orderBy('id')
            ->get()
            ->map(function (VCategory $category) use ($lang) {
                $translation = $category->translations->firstWhere('language_code', $lang)
                    ?? $category->translations->firstWhere('language_code', 'en')
                    ?? $category->translations->first();

                return [
                    'id' => (int) $category->id,
                    'title' => (string) ($translation?->title ?? ''),
                    'words_count' => (int) ($category->words_count ?? 0),
                    'completed_words_count' => (int) ($completedByCategory[$category->id] ?? 0),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'language' => $lang,
            'data' => $categories,
        ]);
    }

    public function categoryWords(Request $request, VCategory $category): JsonResponse
    {
        $supported = $this->supportedLanguageCodes();

        $payload = [
            'lang' => strtolower(trim((string) $request->query('lang'))),
            'per_page' => $request->query('per_page'),
        ];

        $validator = Validator::make($payload, [
            'lang' => ['required', 'string', 'max:10', Rule::in($supported)],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $lang = strtolower(trim((string) $validator->validated()['lang']));
        $perPage = (int) ($validator->validated()['per_page'] ?? 50);
        $userId = optional($request->user())->id;

        $query = DB::table('vocabulary_words')
            ->where('vocabulary_words.category_id', $category->id)
            ->leftJoin('v_word_translations as t', function ($join) use ($lang) {
                $join->on('vocabulary_words.id', '=', 't.word_id')
                    ->where('t.language_code', '=', $lang);
            })
            ->leftJoin('v_word_translations as te', function ($join) {
                $join->on('vocabulary_words.id', '=', 'te.word_id')
                    ->where('te.language_code', '=', 'en');
            })
            ->leftJoin('vocabulary_word_completions as c', function ($join) use ($userId) {
                $join->on('vocabulary_words.id', '=', 'c.word_id');
                if ($userId) {
                    $join->where('c.user_id', '=', $userId);
                } else {
                    $join->whereRaw('1 = 0');
                }
            })
            ->orderBy('vocabulary_words.id')
            ->select([
                'vocabulary_words.id',
                DB::raw("COALESCE(t.word_text, te.word_text, '') as word"),
                DB::raw("COALESCE(t.meaning_text, te.meaning_text, '') as meaning"),
                DB::raw('CASE WHEN c.id IS NULL THEN 0 ELSE 1 END as is_completed'),
            ]);

        $paginated = $query->paginate($perPage);

        return response()->json([
            'category_id' => $category->id,
            'language' => $lang,
            'words' => collect($paginated->items())->map(function ($row) {
                return [
                    'id' => (int) $row->id,
                    'word' => (string) ($row->word ?? ''),
                    'meaning' => (string) ($row->meaning ?? ''),
                    'is_completed' => (bool) ((int) ($row->is_completed ?? 0)),
                ];
            })->values(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function completeWord(Request $request, VocabularyWord $word): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        VocabularyWordCompletion::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'word_id' => $word->id,
            ],
            [
                'completed_at' => now(),
            ],
        );

        return response()->json([
            'success' => true,
        ]);
    }

    private function supportedLanguageCodes(): array
    {
        return [
            'tr',
            'en',
            'de',
            'ar',
            'bs',
            'sq',
            'it',
        ];
    }
}
