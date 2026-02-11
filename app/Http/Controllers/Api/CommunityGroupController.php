<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CommunityGroupController extends Controller
{
    /**
     * Display a listing of community groups.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CommunityGroup::with(['creator', 'posts'])->withCount(['posts', 'members']);

        // Filter by search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        $groups = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $groups->items(),
            'pagination' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'per_page' => $groups->perPage(),
                'total' => $groups->total(),
            ],
        ]);
    }

    /**
     * Store a newly created community group.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:community_groups',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'max_members' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('community-groups', 'public');
        }

        $group = CommunityGroup::create($validated);
        $group->load(['user', 'posts']);

        return response()->json([
            'success' => true,
            'message' => 'Community group created successfully',
            'data' => $group,
        ], 201);
    }

    /**
     * Display the specified community group.
     */
    public function show(CommunityGroup $communityGroup): JsonResponse
    {
        $communityGroup->load(['user', 'posts.user', 'posts.comments']);
        $communityGroup->loadCount(['posts', 'members']);

        return response()->json([
            'success' => true,
            'data' => $communityGroup,
        ]);
    }

    /**
     * Update the specified community group.
     */
    public function update(Request $request, CommunityGroup $communityGroup): JsonResponse
    {
        // Check if user can update this group
        if ($communityGroup->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this group',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:community_groups,name,'.$communityGroup->id,
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'max_members' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : $communityGroup->is_active;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($communityGroup->image) {
                Storage::disk('public')->delete($communityGroup->image);
            }
            $validated['image'] = $request->file('image')->store('community-groups', 'public');
        }

        $communityGroup->update($validated);
        $communityGroup->load(['user', 'posts']);

        return response()->json([
            'success' => true,
            'message' => 'Community group updated successfully',
            'data' => $communityGroup,
        ]);
    }

    /**
     * Remove the specified community group.
     */
    public function destroy(CommunityGroup $communityGroup): JsonResponse
    {
        // Check if user can delete this group
        if ($communityGroup->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this group',
            ], 403);
        }

        // Delete image if exists
        if ($communityGroup->image) {
            Storage::disk('public')->delete($communityGroup->image);
        }

        $communityGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Community group deleted successfully',
        ]);
    }

    /**
     * Get community group categories.
     */
    public function categories(): JsonResponse
    {
        $categories = CommunityGroup::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function joinedGroups(Request $request): JsonResponse
    {
        $user = Auth::user();

        $query = $user->communityGroups()
            ->with(['creator', 'posts'])
            ->withCount(['posts', 'members']);

        if ($request->filled('search')) {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        $groups = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }

    public function membership(CommunityGroup $communityGroup): JsonResponse
    {
        $user = Auth::user();
        $isMember = $communityGroup->members()->where('user_id', $user->id)->exists();

        return response()->json([
            'success' => true,
            'is_member' => $isMember,
        ]);
    }

    /**
     * Join a community group.
     */
    public function join(CommunityGroup $communityGroup): JsonResponse
    {
        $user = Auth::user();

        // Check if already a member
        if ($communityGroup->members()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Already a member of this group',
            ], 400);
        }

        // Check if group is full
        if ($communityGroup->max_members && $communityGroup->members()->count() >= $communityGroup->max_members) {
            return response()->json([
                'success' => false,
                'message' => 'Group is full',
            ], 400);
        }

        $communityGroup->members()->attach($user->id, ['joined_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined the group',
        ]);
    }

    /**
     * Leave a community group.
     */
    public function leave(CommunityGroup $communityGroup): JsonResponse
    {
        $user = Auth::user();

        // Check if user is a member
        if (! $communityGroup->members()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Not a member of this group',
            ], 400);
        }

        $communityGroup->members()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully left the group',
        ]);
    }
}
