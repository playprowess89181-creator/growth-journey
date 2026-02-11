@extends('layouts.admin')

@section('title', 'Edit Community Post')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Community Post</h1>
                    <p class="text-gray-600">Update your community post content and settings</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.community.posts.show', $post) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 shadow-sm border border-blue-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View Post
                    </a>
                    <a href="{{ route('admin.community.posts.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 shadow-sm border border-gray-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Posts
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8">
                <form action="{{ route('admin.community.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Post Title *
                                </label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $post->title) }}"
                                       required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('title') border-red-500 @enderror"
                                       placeholder="Enter post title...">
                                @error('title')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Community Group -->
                            <div>
                                <label for="community_group_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Community Group *
                                </label>
                                <select id="community_group_id" 
                                        name="community_group_id" 
                                        required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('community_group_id') border-red-500 @enderror">
                                    <option value="">Select a community group</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" {{ old('community_group_id', $post->community_group_id) == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('community_group_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Publishing Options -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-800">Publishing Options</h3>
                                
                                <!-- Published Status -->
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="is_published" 
                                               value="1" 
                                               {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                                               class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                        <span class="ml-3 text-sm font-semibold text-gray-700">Published</span>
                                    </label>
                                    <p class="text-gray-500 text-sm mt-1">Uncheck to save as draft</p>
                                </div>

                                <!-- Pinned Status -->
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="is_pinned" 
                                               value="1" 
                                               {{ old('is_pinned', $post->is_pinned) ? 'checked' : '' }}
                                               class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                        <span class="ml-3 text-sm font-semibold text-gray-700">Pin this post</span>
                                    </label>
                                    <p class="text-gray-500 text-sm mt-1">Pinned posts appear at the top of the group</p>
                                </div>

                                <!-- Publish Date -->
                                <div id="publish-date-section" class="{{ old('is_published', $post->is_published) ? '' : 'hidden' }}">
                                    <label for="published_at" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Publish Date & Time
                                    </label>
                                    <input type="datetime-local" 
                                           id="published_at" 
                                           name="published_at" 
                                           value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('published_at') border-red-500 @enderror">
                                    @error('published_at')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-gray-500 text-sm mt-1">Leave empty to publish immediately when status is set to published</p>
                                </div>
                            </div>

                            <!-- Post Stats -->
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Post Statistics</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Comments:</span>
                                        <span class="font-semibold text-gray-900 ml-1">{{ $post->comments_count ?? 0 }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Created:</span>
                                        <span class="font-semibold text-gray-900 ml-1">{{ $post->created_at->format('M j, Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Author:</span>
                                        <span class="font-semibold text-gray-900 ml-1">{{ $post->user->name }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Group:</span>
                                        <span class="font-semibold text-gray-900 ml-1">{{ $post->communityGroup->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Post Image -->
                            <div>
                                <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Post Image
                                </label>
                                
                                @if($post->image)
                                    <!-- Current Image -->
                                    <div id="current-image" class="mb-4">
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $post->image) }}" 
                                                 alt="Current post image" 
                                                 class="max-h-48 max-w-full rounded-lg shadow-lg">
                                            <button type="button" 
                                                    id="remove-current-image" 
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2">Current image</p>
                                        <input type="hidden" name="remove_image" id="remove_image_input" value="0">
                                    </div>
                                @endif

                                <div id="image-upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-purple-400 transition-colors duration-200 {{ $post->image ? 'hidden' : '' }}">
                                    <div id="upload-placeholder" class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <span class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                                <span>{{ $post->image ? 'Change image' : 'Upload a file' }}</span>
                                            </span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                    <!-- Image Preview -->
                                    <div id="image-preview" class="hidden">
                                        <div class="relative">
                                            <img id="preview-image" src="" alt="Preview" class="max-h-48 max-w-full rounded-lg shadow-lg">
                                            <button type="button" id="remove-image" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2 text-center">
                                            <span id="file-name"></span> • <span id="file-size"></span>
                                        </p>
                                        <button type="button" id="change-image" class="mt-2 text-sm text-purple-600 hover:text-purple-500 font-medium">
                                            Change Image
                                        </button>
                                    </div>
                                </div>
                                @error('image')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">
                            Post Content *
                        </label>
                        <textarea id="content" 
                                  name="content" 
                                  rows="12"
                                  required
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('content') border-red-500 @enderror"
                                  placeholder="Write your post content here...">{{ old('content', $post->content) }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Share your thoughts, announcements, or discussions with the community</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-8 mt-8 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Update Post
                        </button>
                        <button type="submit" 
                                name="action" 
                                value="draft"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Save as Draft
                        </button>
                        <a href="{{ route('admin.community.posts.index') }}" 
                           class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const imagePreview = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeImageBtn = document.getElementById('remove-image');
    const changeImageBtn = document.getElementById('change-image');
    const uploadArea = document.getElementById('image-upload-area');
    const currentImage = document.getElementById('current-image');
    const removeCurrentImageBtn = document.getElementById('remove-current-image');
    const removeImageInput = document.getElementById('remove_image_input');

    // Publishing options
    const isPublishedCheckbox = document.querySelector('input[name="is_published"]');
    const publishDateSection = document.getElementById('publish-date-section');

    // Function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Function to show image preview
    function showImagePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            
            uploadPlaceholder.classList.add('hidden');
            imagePreview.classList.remove('hidden');
            uploadArea.classList.remove('border-dashed');
            uploadArea.classList.add('border-solid', 'border-purple-300');
        };
        reader.readAsDataURL(file);
    }

    // Function to hide image preview
    function hideImagePreview() {
        uploadPlaceholder.classList.remove('hidden');
        imagePreview.classList.add('hidden');
        uploadArea.classList.add('border-dashed');
        uploadArea.classList.remove('border-solid', 'border-purple-300');
        previewImage.src = '';
        imageInput.value = '';
    }

    // Handle file input change
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file.');
                return;
            }
            
            // Validate file size (2MB limit)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB.');
                return;
            }
            
            showImagePreview(file);
            
            // Hide current image if exists
            if (currentImage) {
                currentImage.classList.add('hidden');
            }
        }
    });

    // Handle remove image button
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            hideImagePreview();
            
            // Show current image if exists
            if (currentImage) {
                currentImage.classList.remove('hidden');
            }
        });
    }

    // Handle change image button
    if (changeImageBtn) {
        changeImageBtn.addEventListener('click', function() {
            imageInput.click();
        });
    }

    // Handle remove current image button
    if (removeCurrentImageBtn) {
        removeCurrentImageBtn.addEventListener('click', function() {
            currentImage.classList.add('hidden');
            uploadArea.classList.remove('hidden');
            removeImageInput.value = '1';
        });
    }

    // Handle drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-purple-400', 'bg-purple-50');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-purple-400', 'bg-purple-50');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-purple-400', 'bg-purple-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file.');
                return;
            }
            
            // Validate file size (2MB limit)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB.');
                return;
            }
            
            // Set the file to the input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            imageInput.files = dataTransfer.files;
            
            showImagePreview(file);
            
            // Hide current image if exists
            if (currentImage) {
                currentImage.classList.add('hidden');
            }
        }
    });

    // Handle click on upload area to trigger file input
    uploadArea.addEventListener('click', function(e) {
        // Only trigger if we're not clicking on buttons in the preview area
        if (!e.target.closest('#image-preview')) {
            imageInput.click();
        }
    });

    // Handle publish date visibility
    function togglePublishDateSection() {
        if (isPublishedCheckbox.checked) {
            publishDateSection.classList.remove('hidden');
        } else {
            publishDateSection.classList.add('hidden');
        }
    }

    // Initial state
    togglePublishDateSection();

    // Listen for changes
    isPublishedCheckbox.addEventListener('change', togglePublishDateSection);
});
</script>
@endsection