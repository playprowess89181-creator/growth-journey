@extends('layouts.admin')

@section('title', 'Create Community Post')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Community Post</h1>
                    <p class="text-gray-600">Share content with your community groups</p>
                </div>
                <a href="{{ route('admin.community.posts.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200 shadow-sm border border-gray-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Posts
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-8">
                <form action="{{ route('admin.community.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf

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
                                       value="{{ old('title') }}"
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
                                        <option value="{{ $group->id }}" {{ old('community_group_id') == $group->id ? 'selected' : '' }}>
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
                                               {{ old('is_published', true) ? 'checked' : '' }}
                                               class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                        <span class="ml-3 text-sm font-semibold text-gray-700">Publish immediately</span>
                                    </label>
                                    <p class="text-gray-500 text-sm mt-1">Uncheck to save as draft</p>
                                </div>

                                <!-- Pinned Status -->
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="is_pinned" 
                                               value="1" 
                                               {{ old('is_pinned') ? 'checked' : '' }}
                                               class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                        <span class="ml-3 text-sm font-semibold text-gray-700">Pin this post</span>
                                    </label>
                                    <p class="text-gray-500 text-sm mt-1">Pinned posts appear at the top of the group</p>
                                </div>

                                <!-- Publish Date -->
                                <div id="publish-date-section" class="hidden">
                                    <label for="published_at" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Publish Date & Time
                                    </label>
                                    <input type="datetime-local" 
                                           id="published_at" 
                                           name="published_at" 
                                           value="{{ old('published_at') }}"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('published_at') border-red-500 @enderror">
                                    @error('published_at')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-gray-500 text-sm mt-1">Leave empty to publish immediately when status is set to published</p>
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
                                <div id="image-upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-purple-400 transition-colors duration-200">
                                    <div id="upload-placeholder" class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <span class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                                <span>Upload a file</span>
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
                                  placeholder="Write your post content here...">{{ old('content') }}</textarea>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Post
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
        }
    });

    // Handle remove image button
    removeImageBtn.addEventListener('click', function() {
        hideImagePreview();
    });

    // Handle change image button
    changeImageBtn.addEventListener('click', function() {
        imageInput.click();
    });

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