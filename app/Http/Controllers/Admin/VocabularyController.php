<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VCategory;
use App\Models\VocabularyWord;
use App\Models\VWordTranslation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VocabularyController extends Controller
{
    public function index()
    {
        return view('admin.content.vocabulary.index');
    }

    public function wordsIndex(Request $request)
    {
        $categories = VCategory::query()
            ->with([
                'translations',
                'words' => function ($query) {
                    $query->orderBy('word_key');
                },
                'words.translations',
            ])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.content.vocabulary.words.index', [
            'categories' => $categories,
            'languages' => $this->languages(),
        ]);
    }

    public function showWord(VocabularyWord $word)
    {
        $word->load(['translations', 'category.translations']);

        return view('admin.content.vocabulary.words.show', [
            'word' => $word,
            'category' => $word->category,
            'languages' => $this->languages(),
        ]);
    }

    public function updateWord(Request $request, VocabularyWord $word)
    {
        $data = $request->validate([
            'word_key' => [
                'required',
                'string',
                'max:120',
                Rule::unique('vocabulary_words', 'word_key')
                    ->where('category_id', $word->category_id)
                    ->ignore($word->id),
            ],
            'translations' => ['nullable', 'array'],
            'translations.*.word_text' => ['nullable', 'string', 'max:255'],
            'translations.*.meaning_text' => ['nullable', 'string'],
        ]);

        $word->update([
            'word_key' => $data['word_key'],
        ]);

        $this->upsertWordTranslations($word, $data['translations'] ?? []);

        return redirect()
            ->route('admin.vocabulary.words.show', $word)
            ->with('success', 'Word updated successfully.');
    }

    public function destroyWord(VocabularyWord $word)
    {
        $categoryId = $word->category_id;
        $word->delete();

        return redirect()
            ->route('admin.vocabulary.words.index', ['category_id' => $categoryId])
            ->with('success', 'Word deleted successfully.');
    }

    private function languages(): array
    {
        return [
            'en' => 'English',
            'tr' => 'Turkish',
            'de' => 'German',
            'ar' => 'Arabic',
            'bs' => 'Bosnian',
            'sq' => 'Albanian',
            'it' => 'Italian',
        ];
    }

    private function upsertWordTranslations(VocabularyWord $word, array $translations): void
    {
        $supported = array_keys($this->languages());

        foreach ($translations as $code => $payload) {
            if (! is_array($payload)) {
                continue;
            }

            $code = strtolower(trim((string) $code));
            if (! in_array($code, $supported, true)) {
                continue;
            }

            $wordText = trim((string) ($payload['word_text'] ?? ''));
            $meaningText = trim((string) ($payload['meaning_text'] ?? ''));

            if ($wordText === '' && $meaningText === '') {
                VWordTranslation::query()
                    ->where('word_id', $word->id)
                    ->where('language_code', $code)
                    ->delete();
                continue;
            }

            $word->translations()->updateOrCreate(
                ['language_code' => $code],
                ['word_text' => $wordText, 'meaning_text' => $meaningText],
            );
        }
    }
}
