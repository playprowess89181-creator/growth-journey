<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index(Request $request)
    {
        $query = Report::with(['user', 'reportable', 'reviewer'])
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by reportable type if provided
        if ($request->filled('type')) {
            $query->where('reportable_type', $request->type);
        }

        $reports = $query->paginate(15);

        // Get statistics for the dashboard
        $stats = [
            'total_reports' => Report::count(),
            'pending_reports' => Report::pending()->count(),
            'reviewed_reports' => Report::reviewed()->count(),
            'resolved_reports' => Report::resolved()->count(),
            'dismissed_reports' => Report::dismissed()->count(),
        ];

        return view('admin.community.reports.index', compact('reports', 'stats'));
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report)
    {
        $report->load(['user', 'reportable', 'reviewer']);

        return view('admin.community.reports.show', compact('report'));
    }

    /**
     * Update the specified report status.
     */
    public function update(Request $request, Report $report)
    {
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

        return redirect()->route('admin.community.reports.index')
            ->with('success', 'Report status updated successfully.');
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()->route('admin.community.reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Bulk update reports status.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'report_ids' => 'required|array',
            'report_ids.*' => 'exists:reports,id',
            'status' => 'required|in:pending,reviewed,resolved,dismissed',
        ]);

        Report::whereIn('id', $request->report_ids)->update([
            'status' => $request->status,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.community.reports.index')
            ->with('success', 'Selected reports updated successfully.');
    }
}
