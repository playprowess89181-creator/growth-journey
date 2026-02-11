<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DialogueTopicRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DialogueTopicRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $requests = DialogueTopicRequest::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (DialogueTopicRequest $request) => $this->serializeRequest($request))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $topicRequest = DialogueTopicRequest::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Topic request submitted',
            'data' => $this->serializeRequest($topicRequest),
        ], 201);
    }

    public function update(Request $request, DialogueTopicRequest $topicRequest): JsonResponse
    {
        if ($topicRequest->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized',
            ], 403);
        }

        if ($topicRequest->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Approved requests cannot be edited',
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $topicRequest->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Topic request updated',
            'data' => $this->serializeRequest($topicRequest),
        ]);
    }

    public function destroy(DialogueTopicRequest $topicRequest): JsonResponse
    {
        if ($topicRequest->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authorized',
            ], 403);
        }

        if ($topicRequest->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Approved requests cannot be deleted',
            ], 409);
        }

        $topicRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topic request deleted',
        ]);
    }

    private function serializeRequest(DialogueTopicRequest $request): array
    {
        return [
            'id' => $request->id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'admin_feedback' => $request->admin_feedback,
            'created_at' => optional($request->created_at)->toIso8601String(),
            'updated_at' => optional($request->updated_at)->toIso8601String(),
        ];
    }
}
