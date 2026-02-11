<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityGroup;
use App\Models\TrackHabit;
use App\Models\TrackHabitEntry;
use App\Models\TrackHabitMemberStat;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class TrackHabitController extends Controller
{
    public function groupHabits(CommunityGroup $communityGroup): JsonResponse
    {
        $habits = $communityGroup->trackHabits()
            ->latest()
            ->get()
            ->map(function ($habit) {
                return [
                    'id' => $habit->id,
                    'name' => $habit->name,
                    'description' => $habit->description,
                    'frequency_type' => $habit->frequency_type,
                    'frequency_label' => $habit->frequency_label,
                    'weekdays' => $habit->weekdays ?? [],
                    'times_per_week' => $habit->times_per_week,
                    'xp' => $habit->xp,
                    'created_at' => optional($habit->created_at)->toIso8601String(),
                    'updated_at' => optional($habit->updated_at)->toIso8601String(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $habits,
        ]);
    }

    public function memberStats(Request $request, CommunityGroup $communityGroup): JsonResponse
    {
        $user = $request->user();
        $habits = $communityGroup->trackHabits()->orderBy('name')->get();
        $weekdays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $entryRows = TrackHabitEntry::where('community_group_id', $communityGroup->id)
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('track_habit_id');

        $latestStats = TrackHabitMemberStat::where('community_group_id', $communityGroup->id)
            ->where('user_id', $user->id)
            ->orderBy('stat_date')
            ->get()
            ->groupBy('track_habit_id')
            ->map(function ($items) {
                return $items->last();
            });

        $habitsPayload = [];
        foreach ($habits as $habit) {
            $weekStatus = array_fill_keys($weekdays, null);
            $entryRow = $entryRows->get($habit->id);
            $entryMap = $this->entryMap($entryRow?->entry_date);
            foreach ($entryMap as $date => $status) {
                $day = Carbon::parse($date);
                if ($day->betweenIncluded($startOfWeek, $endOfWeek)) {
                    $dayKey = strtolower($day->format('D'));
                    $dayKey = substr($dayKey, 0, 3);
                    if (array_key_exists($dayKey, $weekStatus)) {
                        $weekStatus[$dayKey] = $status;
                    }
                }
            }

            $latest = $latestStats->get($habit->id);
            $habitsPayload[(string) $habit->id] = [
                'weekdays' => $weekStatus,
                'streak' => $latest?->streak ?? 0,
                'overall_percentage' => $latest?->overall_percentage ?? 0,
                'is_frozen' => $latest?->is_frozen ?? false,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'weekdays' => $weekdays,
                'habits' => $habitsPayload,
            ],
        ]);
    }

    public function updateMemberStats(
        Request $request,
        CommunityGroup $communityGroup,
        TrackHabit $habit
    ): JsonResponse {
        if ($habit->community_group_id !== $communityGroup->id) {
            abort(404);
        }

        $validated = $request->validate([
            'date' => 'nullable|date',
            'status' => 'required|in:success,fail,clear',
            'streak' => 'required|integer|min:0',
            'overall_percentage' => 'required|integer|min:0|max:100',
        ]);

        $date = isset($validated['date'])
            ? Carbon::parse($validated['date'])->toDateString()
            : Carbon::now()->toDateString();

        $existing = TrackHabitMemberStat::where('community_group_id', $communityGroup->id)
            ->where('track_habit_id', $habit->id)
            ->where('user_id', $request->user()->id)
            ->where('stat_date', $date)
            ->first();

        if ($existing && $existing->is_frozen) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $existing->id,
                    'streak' => $existing->streak,
                    'overall_percentage' => $existing->overall_percentage,
                    'is_frozen' => true,
                ],
            ]);
        }

        $status = $validated['status'] === 'clear' ? null : $validated['status'];

        $entryRow = TrackHabitEntry::firstOrNew([
            'community_group_id' => $communityGroup->id,
            'track_habit_id' => $habit->id,
            'user_id' => $request->user()->id,
        ]);
        $entryMap = $this->entryMap($entryRow->entry_date);
        if ($status === null) {
            unset($entryMap[$date]);
        } else {
            $entryMap[$date] = $status;
        }
        $entryRow->entry_date = $this->entryList($entryMap);
        $entryRow->save();

        $stat = TrackHabitMemberStat::updateOrCreate(
            [
                'community_group_id' => $communityGroup->id,
                'track_habit_id' => $habit->id,
                'user_id' => $request->user()->id,
                'stat_date' => $date,
            ],
            [
                'status' => $status,
                'streak' => $validated['streak'],
                'overall_percentage' => $validated['overall_percentage'],
                'is_frozen' => false,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $stat->id,
                'streak' => $stat->streak,
                'overall_percentage' => $stat->overall_percentage,
                'is_frozen' => $stat->is_frozen,
            ],
        ]);
    }

    public function habitEntries(
        Request $request,
        CommunityGroup $communityGroup,
        TrackHabit $habit
    ): JsonResponse {
        if ($habit->community_group_id !== $communityGroup->id) {
            abort(404);
        }

        $user = $request->user();
        $monthParam = $request->query('month');
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

        $entryRow = TrackHabitEntry::where('community_group_id', $communityGroup->id)
            ->where('track_habit_id', $habit->id)
            ->where('user_id', $user->id)
            ->first();
        $entryMap = $this->entryMap($entryRow?->entry_date);
        $monthEntries = [];
        foreach ($entryMap as $date => $status) {
            $day = Carbon::parse($date);
            if ($day->betweenIncluded($startOfMonth, $endOfMonth)) {
                $monthEntries[$date] = $status;
            }
        }

        $latest = TrackHabitMemberStat::where('community_group_id', $communityGroup->id)
            ->where('track_habit_id', $habit->id)
            ->where('user_id', $user->id)
            ->orderBy('stat_date', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'month' => $monthParam,
                'entries' => $monthEntries,
                'streak' => $latest?->streak ?? 0,
                'overall_percentage' => $latest?->overall_percentage ?? 0,
                'is_frozen' => $latest?->is_frozen ?? false,
            ],
        ]);
    }

    private function entryMap($value): array
    {
        if (is_array($value)) {
            return $this->mapFromList($value);
        }
        return [];
    }

    private function mapFromList(array $items): array
    {
        $map = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $date = $item['date'] ?? null;
            $status = $item['status'] ?? null;
            if (is_string($date) && $date !== '') {
                $map[$date] = $status;
            }
        }
        return $map;
    }

    private function entryList(array $map): array
    {
        $list = [];
        foreach ($map as $date => $status) {
            $list[] = [
                'date' => $date,
                'status' => $status,
            ];
        }
        return $list;
    }
}
