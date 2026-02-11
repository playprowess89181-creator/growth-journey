<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use App\Models\PrayerRequestComment;
use App\Models\PrayerRequestPrayer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PrayerRequestController extends Controller
{
    public const CATEGORIES = [
        'general',
        'family',
        'healing',
        'gratitude',
        'guidance',
        'work',
    ];

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:200',
            'category' => 'nullable|string|in:'.implode(',', self::CATEGORIES),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = PrayerRequest::query()
            ->where('status', 'active')
            ->where('is_public', true)
            ->with(['user:id,name'])
            ->withCount([
                'prayers as prayer_count',
                'approvedComments as comments_count',
            ])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $items->getCollection()->map(fn (PrayerRequest $r) => $this->serializeRequest($r))->values(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function mine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:200',
            'category' => 'nullable|string|in:'.implode(',', self::CATEGORIES),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = PrayerRequest::query()
            ->where('user_id', Auth::id())
            ->with(['user:id,name'])
            ->withCount([
                'prayers as prayer_count',
                'approvedComments as comments_count',
            ])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = (string) $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $items = $query->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $items->getCollection()->map(fn (PrayerRequest $r) => $this->serializeRequest($r))->values(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:120',
            'description' => 'required|string|max:2000',
            'category' => 'required|string|in:'.implode(',', self::CATEGORIES),
            'is_public' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $prayerRequest = PrayerRequest::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'is_public' => (bool) $validated['is_public'],
            'status' => 'active',
        ]);

        $prayerRequest->load(['user:id,name'])->loadCount([
            'prayers as prayer_count',
            'approvedComments as comments_count',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Prayer request created successfully',
            'data' => $this->serializeRequest($prayerRequest),
        ], 201);
    }

    public function pray(PrayerRequest $prayerRequest): JsonResponse
    {
        if ($prayerRequest->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This prayer request is not available',
            ], 404);
        }

        $exists = PrayerRequestPrayer::query()
            ->where('prayer_request_id', $prayerRequest->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'already_prayed' => true,
                'message' => 'You already prayed for this request',
            ], 409);
        }

        PrayerRequestPrayer::create([
            'prayer_request_id' => $prayerRequest->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Prayer added',
        ]);
    }

    public function comments(PrayerRequest $prayerRequest): JsonResponse
    {
        $comments = PrayerRequestComment::query()
            ->where('prayer_request_id', $prayerRequest->id)
            ->where('is_approved', true)
            ->with(['user:id,name'])
            ->orderBy('created_at')
            ->get()
            ->map(fn (PrayerRequestComment $c) => $this->serializeComment($c))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $comments,
        ]);
    }

    public function addComment(Request $request, PrayerRequest $prayerRequest): JsonResponse
    {
        if ($prayerRequest->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This prayer request is not available',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment = PrayerRequestComment::create([
            'prayer_request_id' => $prayerRequest->id,
            'user_id' => Auth::id(),
            'comment' => $validator->validated()['comment'],
            'is_approved' => true,
        ]);

        $comment->load(['user:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => $this->serializeComment($comment),
        ], 201);
    }

    private function serializeRequest(PrayerRequest $r): array
    {
        return [
            'id' => $r->id,
            'title' => $r->title,
            'description' => $r->description,
            'category' => $r->category,
            'is_public' => (bool) $r->is_public,
            'status' => $r->status,
            'user_id' => $r->user_id,
            'user_name' => $r->user?->name ?? 'Anonymous',
            'prayer_count' => (int) ($r->prayer_count ?? 0),
            'comments_count' => (int) ($r->comments_count ?? 0),
            'created_at' => optional($r->created_at)->toIso8601String(),
            'updated_at' => optional($r->updated_at)->toIso8601String(),
        ];
    }

    private function serializeComment(PrayerRequestComment $c): array
    {
        return [
            'id' => $c->id,
            'prayer_request_id' => $c->prayer_request_id,
            'user_id' => $c->user_id,
            'user_name' => $c->user?->name ?? 'Anonymous',
            'comment' => $c->comment,
            'created_at' => optional($c->created_at)->toIso8601String(),
            'updated_at' => optional($c->updated_at)->toIso8601String(),
        ];
    }
}
