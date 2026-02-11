<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommunityGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CommunityGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CommunityGroup::with('creator');

        // Filter by search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $groups = $query->latest()->paginate(12);

        // Get filter options
        $categories = CommunityGroup::distinct()->pluck('category')->filter();
        return view('admin.community.groups.index', compact('groups', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.community.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:community_groups',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'max_members' => 'nullable|integer|min:1|max:10000',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('community-groups', 'public');
        }

        CommunityGroup::create($validated);

        return redirect()->route('admin.community.groups.index')
            ->with('success', 'Community group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CommunityGroup $group)
    {
        $group->load('creator');

        $totalMembers = $group->members()->count();
        $recentPosts = $group->posts()->where('created_at', '>=', now()->subDays(7))->count();

        $postsLast30Days = $group->posts()->where('created_at', '>=', now()->subDays(30))->count();
        $commentsLast30Days = Comment::whereHas('communityPost', function ($query) use ($group) {
            $query->where('community_group_id', $group->id);
        })->where('created_at', '>=', now()->subDays(30))->count();

        $joinsLast30Days = $group->members()->wherePivot('created_at', '>=', now()->subDays(30))->count();

        $rawActivityScore = ($postsLast30Days * 10) + ($commentsLast30Days * 2) + ($joinsLast30Days * 5);
        $activityScore = min(100, $rawActivityScore);

        $members = $group->members()
            ->orderByPivot('joined_at', 'desc')
            ->orderByPivot('created_at', 'desc')
            ->take(12)
            ->get();

        return view('admin.community.groups.show', compact(
            'group',
            'totalMembers',
            'recentPosts',
            'activityScore',
            'members',
        ));
    }

    public function removeMember(CommunityGroup $group, User $user)
    {
        $group->members()->detach($user->id);

        return back()->with('success', 'Member removed successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CommunityGroup $group)
    {
        return view('admin.community.groups.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CommunityGroup $group)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('community_groups')->ignore($group->id)],
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'max_members' => 'nullable|integer|min:1|max:10000',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($group->image) {
                Storage::disk('public')->delete($group->image);
            }
            $validated['image'] = $request->file('image')->store('community-groups', 'public');
        }

        $group->update($validated);

        return redirect()->route('admin.community.groups.index')
            ->with('success', 'Community group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommunityGroup $group)
    {
        // Delete associated image if exists
        if ($group->image) {
            Storage::disk('public')->delete($group->image);
        }

        $group->delete();

        return redirect()->route('admin.community.groups.index')
            ->with('success', 'Community group deleted successfully.');
    }

    /**
     * Toggle the active status of a group.
     */
    public function toggleStatus(CommunityGroup $group)
    {
        $group->update(['is_active' => ! $group->is_active]);

        $status = $group->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Community group {$status} successfully.");
    }

    /**
     * Bulk activate selected groups.
     */
    public function bulkActivate(Request $request)
    {
        try {
            $request->validate([
                'group_ids' => 'required|array|min:1',
                'group_ids.*' => 'exists:community_groups,id',
            ]);

            $groupIds = $request->input('group_ids');
            $count = CommunityGroup::whereIn('id', $groupIds)
                ->update(['is_active' => true]);

            return redirect()->back()
                ->with('success', "{$count} community group".($count > 1 ? 's' : '').' activated successfully.');
        } catch (\Exception $e) {
            \Log::error('Bulk activate error: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while activating groups. Please try again.');
        }
    }

    /**
     * Bulk deactivate selected groups.
     */
    public function bulkDeactivate(Request $request)
    {
        try {
            $request->validate([
                'group_ids' => 'required|array|min:1',
                'group_ids.*' => 'exists:community_groups,id',
            ]);

            $groupIds = $request->input('group_ids');
            $count = CommunityGroup::whereIn('id', $groupIds)
                ->update(['is_active' => false]);

            return redirect()->back()
                ->with('success', "{$count} community group".($count > 1 ? 's' : '').' deactivated successfully.');
        } catch (\Exception $e) {
            \Log::error('Bulk deactivate error: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while deactivating groups. Please try again.');
        }
    }

    /**
     * Bulk delete selected groups.
     */
    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'group_ids' => 'required|array|min:1',
                'group_ids.*' => 'exists:community_groups,id',
            ]);

            $groupIds = $request->input('group_ids');

            // Get groups to delete their images
            $groups = CommunityGroup::whereIn('id', $groupIds)->get();

            // Delete images from storage
            foreach ($groups as $group) {
                if ($group->image && Storage::exists($group->image)) {
                    Storage::delete($group->image);
                }
            }

            $count = CommunityGroup::whereIn('id', $groupIds)->delete();

            return redirect()->back()
                ->with('success', "{$count} community group".($count > 1 ? 's' : '').' deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Bulk delete error: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'An error occurred while deleting groups. Please try again.');
        }
    }
}
