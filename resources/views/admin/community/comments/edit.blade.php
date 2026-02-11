@extends('layouts.admin')

@section('title', 'Edit Comment')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Comment</h1>
                    <p class="text-gray-600">Review and moderate this comment</p>
                </div>
                <div class="flex gap-3">
                    @if($comment->communityPost && !$comment->communityPost->trashed())
                        <a href="{{ route('admin.community.posts.show', $comment->communityPost) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 shadow-sm border border-blue-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Post
                        </a>
                    @endif
                    <a href="{{ route('admin.community.comments.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 shadow-sm border border-gray-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Comments
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Comment Content -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <form action="{{ route('admin.community.comments.update', $comment) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Comment Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Comment Content</label>
                            <textarea id="content" 
                                      name="content" 
                                      rows="6"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 resize-none"
                                      required>{{ old('content', $comment->content) }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Approval Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Approval Status</label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="is_approved" 
                                           value="1" 
                                           {{ old('is_approved', $comment->is_approved) ? 'checked' : '' }}
                                           class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                    <span class="ml-3 text-sm text-gray-700">
                                        <span class="font-medium text-green-700">Approved</span> - Comment is visible to users
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="is_approved" 
                                           value="0" 
                                           {{ !old('is_approved', $comment->is_approved) ? 'checked' : '' }}
                                           class="w-4 h-4 text-yellow-600 border-gray-300 focus:ring-yellow-500">
                                    <span class="ml-3 text-sm text-gray-700">
                                        <span class="font-medium text-yellow-700">Pending</span> - Comment requires review
                                    </span>
                                </label>
                            </div>
                            @error('is_approved')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <div class="flex space-x-3">
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Update Comment
                                </button>
                                <a href="{{ route('admin.community.comments.index') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Comment Info -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Comment Information</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Author:</span>
                            <span class="font-semibold text-gray-900">{{ $comment->user->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Email:</span>
                            <span class="font-semibold text-gray-900">{{ $comment->user->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Created:</span>
                            <span class="font-semibold text-gray-900">{{ $comment->created_at->format('M j, Y g:i A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $comment->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $comment->is_approved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Post Info -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Post Information</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500 block mb-1">Post Title:</span>
                            @if($comment->communityPost)
                                @if($comment->communityPost->trashed())
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700">{{ $comment->communityPost->title }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            Deleted
                                        </span>
                                    </div>
                                @else
                                    <a href="{{ route('admin.community.posts.show', $comment->communityPost) }}" 
                                       class="font-semibold text-purple-600 hover:text-purple-800 transition-colors">
                                        {{ $comment->communityPost->title }}
                                    </a>
                                @endif
                            @else
                                <span class="font-semibold text-gray-700">Post unavailable</span>
                            @endif
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Community Group:</span>
                            <span class="font-semibold text-gray-900">{{ $comment->communityPost?->communityGroup?->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Post Author:</span>
                            <span class="font-semibold text-gray-900">{{ $comment->communityPost?->user?->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Post Status:</span>
                            @if($comment->communityPost)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $comment->communityPost->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $comment->communityPost->is_published ? 'Published' : 'Draft' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                    —
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if(!$comment->is_approved)
                            <form action="{{ route('admin.community.comments.approve', $comment) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Approve Comment
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.community.comments.reject', $comment) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors duration-200 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject Comment
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.community.comments.destroy', $comment) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this comment? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Comment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
