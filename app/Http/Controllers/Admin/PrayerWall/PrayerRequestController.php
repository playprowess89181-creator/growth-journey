<?php

namespace App\Http\Controllers\Admin\PrayerWall;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrayerRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PrayerRequest::query()->with('user')->withCount(['prayers', 'comments', 'approvedComments']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('visibility') && $request->visibility !== 'all') {
            $query->where('is_public', $request->visibility === 'public');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $requests = $query->orderByDesc('created_at')->paginate(15);

        $categories = PrayerRequest::query()
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.prayer-wall.requests.index', compact('requests', 'categories'));
    }

    public function create()
    {
        $categories = PrayerRequest::query()
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.prayer-wall.requests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => 'required|string|max:50',
            'is_public' => 'required|boolean',
            'status' => 'required|string|max:50',
        ]);

        PrayerRequest::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.prayer-wall.requests.index')
            ->with('success', 'Prayer request created successfully.');
    }

    public function edit(PrayerRequest $request)
    {
        $categories = PrayerRequest::query()
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.prayer-wall.requests.edit', compact('request', 'categories'));
    }

    public function update(Request $httpRequest, PrayerRequest $request)
    {
        $validated = $httpRequest->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => 'required|string|max:50',
            'is_public' => 'required|boolean',
            'status' => 'required|string|max:50',
        ]);

        $request->update($validated);

        return redirect()->route('admin.prayer-wall.requests.index')
            ->with('success', 'Prayer request updated successfully.');
    }

    public function destroy(PrayerRequest $request)
    {
        $request->delete();

        return redirect()->route('admin.prayer-wall.requests.index')
            ->with('success', 'Prayer request deleted successfully.');
    }
}
