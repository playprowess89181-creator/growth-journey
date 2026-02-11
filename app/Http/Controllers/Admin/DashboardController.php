<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $usersQuery = User::query()->where('role', '!=', 'admin');

        // Get dashboard statistics
        $stats = [
            'total_users' => (clone $usersQuery)->count(),
            'active_users' => (clone $usersQuery)->where('created_at', '>=', now()->subDays(30))->count(),
            'total_lessons' => Lesson::count(),
            'total_modules' => Module::count(),
        ];

        $activityWindow = now()->subHours(24);

        Activity::where('created_at', '<', $activityWindow)->delete();

        $activities = Activity::with('user')
            ->where('created_at', '>=', $activityWindow)
            ->latest()
            ->get()
            ->map(function (Activity $activity) {
                $subjectLabel = $activity->subject_type ?: 'Admin action';
                $actionLabel = match ($activity->action) {
                    'created' => 'Created',
                    'updated' => 'Updated',
                    'deleted' => 'Deleted',
                    'published' => 'Published',
                    'unpublished' => 'Unpublished',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'verified' => 'Verified',
                    'pinned' => 'Pinned',
                    'unpinned' => 'Unpinned',
                    'activated' => 'Activated',
                    'deactivated' => 'Deactivated',
                    default => Str::headline($activity->action ?? 'updated'),
                };
                $description = $activity->description ?: trim($actionLabel.' '.$subjectLabel);
                $userLabel = $activity->user?->name ?? 'Admin';
                $tone = match (true) {
                    in_array($activity->action, ['created', 'approved', 'published', 'verified', 'activated'], true) => ['fa-check', 'text-emerald-600', 'bg-emerald-100'],
                    in_array($activity->action, ['deleted', 'rejected', 'unpublished', 'deactivated'], true) => ['fa-trash', 'text-rose-600', 'bg-rose-100'],
                    in_array($activity->action, ['pinned', 'unpinned'], true) => ['fa-thumbtack', 'text-amber-600', 'bg-amber-100'],
                    default => ['fa-pen', 'text-indigo-600', 'bg-indigo-100'],
                };

                return [
                    'title' => $actionLabel.' '.$subjectLabel,
                    'description' => trim($userLabel.' • '.$description),
                    'time' => $activity->created_at,
                    'icon' => $tone[0],
                    'color' => $tone[1],
                    'bg' => $tone[2],
                ];
            });

        $recentActivities = $activities->take(10);
        $remainingActivities = $activities->slice(10)->values();

        return view('admin.dashboard', compact('stats', 'recentActivities', 'remainingActivities'));
    }
}
