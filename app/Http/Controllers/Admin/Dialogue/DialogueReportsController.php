<?php

namespace App\Http\Controllers\Admin\Dialogue;

use App\Http\Controllers\Controller;
use App\Models\DialogueComment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DialogueReportsController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['user', 'reviewer'])
            ->with(['reportable' => function ($morphTo) {
                $morphTo->morphWith([
                    DialogueComment::class => ['topic', 'user'],
                ]);
            }])
            ->where('reportable_type', DialogueComment::class)
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->paginate(15);

        $stats = [
            'total_reports' => Report::where('reportable_type', DialogueComment::class)->count(),
            'pending_reports' => Report::where('reportable_type', DialogueComment::class)->pending()->count(),
            'reviewed_reports' => Report::where('reportable_type', DialogueComment::class)->reviewed()->count(),
            'resolved_reports' => Report::where('reportable_type', DialogueComment::class)->resolved()->count(),
            'dismissed_reports' => Report::where('reportable_type', DialogueComment::class)->dismissed()->count(),
        ];

        return view('admin.dialogue.reports.index', compact('reports', 'stats'));
    }

    public function show(Report $report)
    {
        abort_unless($report->reportable_type === DialogueComment::class, 404);

        $report->load(['user', 'reviewer'])
            ->loadMorph('reportable', [
                DialogueComment::class => ['topic', 'user'],
            ]);

        return view('admin.dialogue.reports.show', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        abort_unless($report->reportable_type === DialogueComment::class, 404);

        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.dialogue.reports.index')
            ->with('success', 'Report status updated successfully.');
    }

    public function destroy(Report $report)
    {
        abort_unless($report->reportable_type === DialogueComment::class, 404);

        $report->delete();

        return redirect()->route('admin.dialogue.reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}
