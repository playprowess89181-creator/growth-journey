<?php

namespace App\Http\Controllers\Admin\PrayerWall;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use App\Models\PrayerRequestPrayer;
use App\Models\User;
use Illuminate\Http\Request;

class PrayerInteractionController extends Controller
{
    public function index(Request $request)
    {
        $query = PrayerRequestPrayer::query()->with(['prayerRequest', 'user']);

        if ($request->filled('prayer_request')) {
            $query->where('prayer_request_id', $request->prayer_request);
        }

        if ($request->filled('author')) {
            $query->where('user_id', $request->author);
        }

        $prayers = $query->orderByDesc('created_at')->paginate(20);

        $requests = PrayerRequest::query()
            ->orderByDesc('created_at')
            ->get(['id', 'title']);

        $authors = User::query()
            ->whereIn('id', PrayerRequestPrayer::query()->select('user_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.prayer-wall.prayers.index', compact('prayers', 'requests', 'authors'));
    }

    public function destroy(PrayerRequestPrayer $prayer)
    {
        $prayer->delete();

        return response()->json(['success' => true]);
    }
}
