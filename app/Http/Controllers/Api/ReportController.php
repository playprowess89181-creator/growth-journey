<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommunityPost;
use App\Models\DialogueComment;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    /**
     * Display a listing of user's reports.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Report::with(['reportable', 'user'])
            ->where('user_id', Auth::id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by reportable type
        if ($request->filled('type')) {
            if ($request->type === 'post') {
                $query->where('reportable_type', CommunityPost::class);
            } elseif ($request->type === 'comment') {
                $query->where('reportable_type', Comment::class);
            } elseif ($request->type === 'dialogue_comment') {
                $query->where('reportable_type', DialogueComment::class);
            }
        }

        $reports = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $reports->items(),
            'pagination' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ],
        ]);
    }

    /**
     * Store a newly created report.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reportable_type' => 'required|in:post,comment,dialogue_comment',
            'reportable_id' => 'required|integer',
            'reason' => 'required|string|in:spam,harassment,inappropriate_content,misinformation,hate_speech,violence,copyright,other',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $reportableClass = match ($validated['reportable_type']) {
            'post' => CommunityPost::class,
            'comment' => Comment::class,
            'dialogue_comment' => DialogueComment::class,
        };

        // Check if reportable exists
        $reportable = $reportableClass::find($validated['reportable_id']);
        if (! $reportable) {
            return response()->json([
                'success' => false,
                'message' => 'The reported content does not exist',
            ], 404);
        }

        // Check if user already reported this content
        $existingReport = Report::where('user_id', Auth::id())
            ->where('reportable_type', $reportableClass)
            ->where('reportable_id', $validated['reportable_id'])
            ->first();

        if ($existingReport) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reported this content',
            ], 409);
        }

        // Create the report
        $report = Report::create([
            'user_id' => Auth::id(),
            'reportable_type' => $reportableClass,
            'reportable_id' => $validated['reportable_id'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        $report->load(['reportable', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Report submitted successfully',
            'data' => $report,
        ], 201);
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report): JsonResponse
    {
        // Check if user owns this report
        if ($report->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $report->load(['reportable', 'user', 'reviewer']);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Get report reasons for dropdown.
     */
    public function reasons(): JsonResponse
    {
        $reasons = [
            'spam' => 'Spam',
            'harassment' => 'Harassment or Bullying',
            'inappropriate_content' => 'Inappropriate Content',
            'misinformation' => 'Misinformation',
            'hate_speech' => 'Hate Speech',
            'violence' => 'Violence or Threats',
            'copyright' => 'Copyright Violation',
            'other' => 'Other',
        ];

        return response()->json([
            'success' => true,
            'data' => $reasons,
        ]);
    }

    /**
     * Check if user can report specific content.
     */
    public function canReport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reportable_type' => 'required|in:post,comment,dialogue_comment',
            'reportable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $reportableClass = match ($request->reportable_type) {
            'post' => CommunityPost::class,
            'comment' => Comment::class,
            'dialogue_comment' => DialogueComment::class,
        };

        // Check if content exists
        $reportable = $reportableClass::find($request->reportable_id);
        if (! $reportable) {
            return response()->json([
                'success' => false,
                'can_report' => false,
                'message' => 'Content does not exist',
            ]);
        }

        // Check if user already reported this content
        $existingReport = Report::where('user_id', Auth::id())
            ->where('reportable_type', $reportableClass)
            ->where('reportable_id', $request->reportable_id)
            ->first();

        $canReport = ! $existingReport;

        return response()->json([
            'success' => true,
            'can_report' => $canReport,
            'message' => $canReport ? 'You can report this content' : 'You have already reported this content',
        ]);
    }
}
