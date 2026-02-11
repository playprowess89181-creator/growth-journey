<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityGroup;
use App\Models\TrackHabit;
use App\Models\TrackHabitEntry;
use App\Models\TrackHabitMemberStat;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class TrackHabitController extends Controller
{
    public function index()
    {
        $groups = CommunityGroup::withCount(['members', 'trackHabits'])
            ->orderBy('name')
            ->get();
        $totalHabits = $groups->sum('track_habits_count');

        return view('admin.track-habits.index', compact('groups', 'totalHabits'));
    }

    public function show(CommunityGroup $group)
    {
        $habits = $group->trackHabits()->latest()->get();
        $habitsCount = $habits->count();
        $members = $group->members()->orderBy('name')->get();
        $memberStats = [];

        if ($members->isNotEmpty() && $habits->isNotEmpty()) {
            $stats = TrackHabitMemberStat::where('community_group_id', $group->id)
                ->whereIn('user_id', $members->pluck('id'))
                ->orderBy('stat_date')
                ->get();

            foreach ($members as $member) {
                $memberStatsCollection = $stats->where('user_id', $member->id);
                if ($memberStatsCollection->isEmpty()) {
                    $memberStats[$member->id] = [
                        'streak' => 0,
                        'overall' => 0,
                    ];
                    continue;
                }

                $latestByHabit = $memberStatsCollection
                    ->groupBy('track_habit_id')
                    ->map(function ($items) {
                        return $items->last();
                    });

                $memberStats[$member->id] = [
                    'streak' => (int) round($latestByHabit->avg('streak') ?? 0),
                    'overall' => (int) round($latestByHabit->avg('overall_percentage') ?? 0),
                ];
            }
        }

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $monthLabel = $monthStart->format('F Y');
        $memberIds = $members->pluck('id');
        $habitIds = $habits->pluck('id');

        $monthStats = TrackHabitMemberStat::where('community_group_id', $group->id)
            ->when($memberIds->isNotEmpty(), fn ($query) => $query->whereIn('user_id', $memberIds))
            ->when($habitIds->isNotEmpty(), fn ($query) => $query->whereIn('track_habit_id', $habitIds))
            ->whereBetween('stat_date', [$monthStart, $monthEnd])
            ->orderBy('stat_date')
            ->get();

        $latestMonthStats = $monthStats
            ->groupBy(fn ($stat) => $stat->user_id.'-'.$stat->track_habit_id)
            ->map(fn ($items) => $items->last())
            ->values();

        $memberCompletion = [];
        $memberStreak = [];
        foreach ($members as $member) {
            $statsForMember = $latestMonthStats->where('user_id', $member->id);
            $memberCompletion[$member->id] = $statsForMember->isNotEmpty()
                ? (int) round($statsForMember->avg('overall_percentage'))
                : 0;
            $memberStreak[$member->id] = $statsForMember->isNotEmpty()
                ? (int) round($statsForMember->avg('streak'))
                : 0;
        }

        $averageCompletion = $latestMonthStats->isNotEmpty()
            ? (int) round($latestMonthStats->avg('overall_percentage'))
            : 0;

        $leaderboard = $members
            ->map(fn ($member) => [
                'member' => $member,
                'value' => $memberStreak[$member->id] ?? 0,
            ])
            ->sortByDesc('value')
            ->values()
            ->take(5);

        $consistentMembers = $members
            ->map(fn ($member) => [
                'member' => $member,
                'value' => $memberCompletion[$member->id] ?? 0,
            ])
            ->sortByDesc('value')
            ->values()
            ->take(5);

        $habitCompletionCounts = [];
        foreach ($habitIds as $habitId) {
            $habitCompletionCounts[$habitId] = 0;
        }

        $entryRows = TrackHabitEntry::where('community_group_id', $group->id)
            ->when($memberIds->isNotEmpty(), fn ($query) => $query->whereIn('user_id', $memberIds))
            ->when($habitIds->isNotEmpty(), fn ($query) => $query->whereIn('track_habit_id', $habitIds))
            ->get();

        foreach ($entryRows as $entryRow) {
            $entries = is_array($entryRow->entry_date) ? $entryRow->entry_date : [];
            foreach ($entries as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $date = $item['date'] ?? null;
                $status = $item['status'] ?? null;
                if (!is_string($date) || $status !== 'success') {
                    continue;
                }
                $entryDate = Carbon::parse($date);
                if (!$entryDate->betweenIncluded($monthStart, $monthEnd)) {
                    continue;
                }
                $habitId = $entryRow->track_habit_id;
                $habitCompletionCounts[$habitId] = ($habitCompletionCounts[$habitId] ?? 0) + 1;
            }
        }

        $totalSuccesses = array_sum($habitCompletionCounts);
        $topHabits = $habits
            ->map(fn ($habit) => [
                'habit' => $habit,
                'count' => $habitCompletionCounts[$habit->id] ?? 0,
                'percent' => $totalSuccesses > 0
                    ? (int) round((($habitCompletionCounts[$habit->id] ?? 0) / $totalSuccesses) * 100)
                    : 0,
            ])
            ->sortByDesc('count')
            ->values()
            ->take(5);

        return view('admin.track-habits.show', compact(
            'group',
            'habits',
            'habitsCount',
            'members',
            'memberStats',
            'monthLabel',
            'averageCompletion',
            'leaderboard',
            'consistentMembers',
            'topHabits'
        ));
    }

    public function create(CommunityGroup $group)
    {
        return view('admin.track-habits.create', [
            'group' => $group,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request, CommunityGroup $group)
    {
        $validated = $this->validateHabit($request);
        $group->trackHabits()->create($this->habitPayload($validated));

        return redirect()
            ->route('admin.track-habits.show', $group)
            ->with('success', 'Habit created successfully.');
    }

    public function edit(CommunityGroup $group, $habit)
    {
        $habit = TrackHabit::where('community_group_id', $group->id)->findOrFail($habit);

        return view('admin.track-habits.create', [
            'group' => $group,
            'habit' => $habit,
            'habitIndex' => $habit->id,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, CommunityGroup $group, $habit)
    {
        $habit = TrackHabit::where('community_group_id', $group->id)->findOrFail($habit);
        $validated = $this->validateHabit($request);
        $habit->update($this->habitPayload($validated));

        return redirect()
            ->route('admin.track-habits.show', $group)
            ->with('success', 'Habit updated successfully.');
    }

    public function destroy(CommunityGroup $group, $habit)
    {
        $habit = TrackHabit::where('community_group_id', $group->id)->findOrFail($habit);
        $habit->delete();

        return redirect()
            ->route('admin.track-habits.show', $group)
            ->with('success', 'Habit deleted successfully.');
    }

    public function freezeStreak(CommunityGroup $group, User $member)
    {
        $isMember = $group->members()->whereKey($member->id)->exists();
        if (!$isMember) {
            abort(404);
        }

        $habits = $group->trackHabits()->get();
        $today = Carbon::now()->toDateString();
        $frozenToday = TrackHabitMemberStat::where('community_group_id', $group->id)
            ->where('user_id', $member->id)
            ->where('stat_date', $today)
            ->where('is_frozen', true)
            ->exists();

        if ($frozenToday) {
            TrackHabitMemberStat::where('community_group_id', $group->id)
                ->where('user_id', $member->id)
                ->where('stat_date', $today)
                ->where('is_frozen', true)
                ->update(['is_frozen' => false]);

            return redirect()
                ->route('admin.track-habits.members.stats', [$group, $member])
                ->with('success', 'Streak unfrozen for today.');
        }

        foreach ($habits as $habit) {
            $latest = TrackHabitMemberStat::where('community_group_id', $group->id)
                ->where('track_habit_id', $habit->id)
                ->where('user_id', $member->id)
                ->orderBy('stat_date', 'desc')
                ->first();

            $streak = $latest?->streak ?? 0;
            $overall = $latest?->overall_percentage ?? 0;

            TrackHabitMemberStat::updateOrCreate(
                [
                    'community_group_id' => $group->id,
                    'track_habit_id' => $habit->id,
                    'user_id' => $member->id,
                    'stat_date' => $today,
                ],
                [
                    'status' => $latest?->status,
                    'streak' => $streak,
                    'overall_percentage' => $overall,
                    'is_frozen' => true,
                ]
            );
        }

        return redirect()
            ->route('admin.track-habits.members.stats', [$group, $member])
            ->with('success', 'Streak frozen for today.');
    }

    public function memberStats(CommunityGroup $group, User $member)
    {
        $isMember = $group->members()->whereKey($member->id)->exists();
        if (!$isMember) {
            abort(404);
        }

        $habits = $group->trackHabits()->orderBy('name')->get();
        $monthParam = request()->query('month');
        if ($monthParam) {
            try {
                $startOfMonth = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
            } catch (\Throwable $e) {
                $startOfMonth = Carbon::now()->startOfMonth();
                $monthParam = $startOfMonth->format('Y-m');
            }
        } else {
            $startOfMonth = Carbon::now()->startOfMonth();
            $monthParam = $startOfMonth->format('Y-m');
        }
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;
        $monthLabel = $startOfMonth->format('F Y');
        $prevMonth = $startOfMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $startOfMonth->copy()->addMonth()->format('Y-m');

        $entries = [];
        foreach ($habits as $habit) {
            $entries[$habit->id] = array_fill(1, $daysInMonth, null);
        }

        $stats = TrackHabitMemberStat::where('community_group_id', $group->id)
            ->where('user_id', $member->id)
            ->orderBy('stat_date')
            ->get();

        $entryRows = TrackHabitEntry::where('community_group_id', $group->id)
            ->where('user_id', $member->id)
            ->get()
            ->keyBy('track_habit_id');

        foreach ($habits as $habit) {
            $entryRow = $entryRows->get($habit->id);
            $entryList = is_array($entryRow?->entry_date) ? $entryRow->entry_date : [];
            foreach ($entryList as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $date = $item['date'] ?? null;
                $status = $item['status'] ?? null;
                if (!is_string($date)) {
                    continue;
                }
                $entryDate = Carbon::parse($date);
                if (!$entryDate->betweenIncluded($startOfMonth, $endOfMonth)) {
                    continue;
                }
                $day = $entryDate->day;
                $entries[$habit->id][$day] = $status;
            }
        }

        $freezeDaysMap = [];
        $freezeStats = TrackHabitMemberStat::where('community_group_id', $group->id)
            ->where('user_id', $member->id)
            ->whereBetween('stat_date', [$startOfMonth, $endOfMonth])
            ->where('is_frozen', true)
            ->get();
        foreach ($freezeStats as $freezeStat) {
            $freezeDay = Carbon::parse($freezeStat->stat_date)->day;
            $freezeDaysMap[$freezeStat->track_habit_id][$freezeDay] = true;
        }

        $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $calendar = [];
        $startWeekday = (int) $startOfMonth->dayOfWeekIso;

        foreach ($habits as $habit) {
            $weeks = [];
            $week = array_fill(0, $startWeekday - 1, null);

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $week[] = [
                    'day' => $day,
                    'status' => ($freezeDaysMap[$habit->id][$day] ?? false)
                        ? 'freeze'
                        : ($entries[$habit->id][$day] ?? null),
                ];

                if (count($week) === 7) {
                    $weeks[] = $week;
                    $week = [];
                }
            }

            if (!empty($week)) {
                while (count($week) < 7) {
                    $week[] = null;
                }
                $weeks[] = $week;
            }

            $calendar[$habit->id] = $weeks;
        }

        $latestByHabit = $stats
            ->groupBy('track_habit_id')
            ->map(function ($items) {
                return $items->last();
            });

        $habitCompletion = $habits->mapWithKeys(function ($habit) use ($latestByHabit) {
            $stat = $latestByHabit->get($habit->id);
            return [$habit->id => $stat?->overall_percentage ?? 0];
        });
        $overallCompletion = $latestByHabit->isNotEmpty()
            ? (int) round($latestByHabit->avg('overall_percentage'))
            : 0;
        $currentStreak = $latestByHabit->isNotEmpty()
            ? (int) round($latestByHabit->avg('streak'))
            : 0;
        $isFrozenToday = TrackHabitMemberStat::where('community_group_id', $group->id)
            ->where('user_id', $member->id)
            ->where('stat_date', Carbon::now()->toDateString())
            ->where('is_frozen', true)
            ->exists();

        return view('admin.track-habits.member-stats', [
            'group' => $group,
            'member' => $member,
            'habits' => $habits,
            'daysInMonth' => $daysInMonth,
            'monthLabel' => $monthLabel,
            'entries' => $entries,
            'habitCompletion' => $habitCompletion,
            'overallCompletion' => $overallCompletion,
            'currentStreak' => $currentStreak,
            'calendar' => $calendar,
            'weekdays' => $weekdays,
            'selectedMonth' => $monthParam,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'isFrozenToday' => $isFrozenToday,
        ]);
    }

    private function validateHabit(Request $request): array
    {
        return $request->validate([
            'habit_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'frequency' => 'required|in:daily,specific,times',
            'weekdays' => 'required_if:frequency,specific|array',
            'weekdays.*' => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
            'times_per_week' => 'required_if:frequency,times|integer|min:1|max:7',
            'xp' => 'required|integer|min:1|max:1000',
        ]);
    }

    private function habitPayload(array $validated): array
    {
        $frequencyLabel = match ($validated['frequency']) {
            'daily' => 'Every day',
            'specific' => implode(', ', $validated['weekdays'] ?? []),
            'times' => 'Times per week: '.$validated['times_per_week'],
        };

        $frequencyType = $validated['frequency'];
        $weekdays = $frequencyType === 'specific' ? ($validated['weekdays'] ?? []) : [];
        $timesPerWeek = $frequencyType === 'times' ? ($validated['times_per_week'] ?? null) : null;

        return [
            'name' => $validated['habit_name'],
            'description' => $validated['description'] ?? null,
            'frequency_label' => $frequencyLabel,
            'frequency_type' => $frequencyType,
            'weekdays' => $weekdays,
            'times_per_week' => $timesPerWeek,
            'xp' => $validated['xp'],
        ];
    }
}
