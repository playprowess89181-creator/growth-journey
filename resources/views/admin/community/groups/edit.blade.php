@extends('layouts.admin')

@section('title', 'Edit Community Group')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Page Header -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 sticky top-0 z-30">
        <div class="px-6 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                        Edit Community Group
                    </h1>
                    <p class="text-gray-600 mt-1">Update group information and settings</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.community.groups.show', $group) }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-100 text-blue-700 font-semibold rounded-xl hover:bg-blue-200 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View Group
                    </a>
                    <a href="{{ route('admin.community.groups.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Groups
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <form action="{{ route('admin.community.groups.update', $group) }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Group Name -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Group Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $group->name) }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('name') border-red-500 @enderror"
                                       placeholder="Enter group name"
                                       required>
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="category" 
                                       name="category" 
                                       value="{{ old('category', $group->category) }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('category') border-red-500 @enderror"
                                       placeholder="e.g., worship, youth, bible-study"
                                       required>
                                @error('category')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Max Members -->
                            <div>
                                <label for="max_members" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Maximum Members
                                </label>
                                <input type="number" 
                                       id="max_members" 
                                       name="max_members" 
                                       value="{{ old('max_members', $group->max_members) }}"
                                       min="1" 
                                       max="10000"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('max_members') border-red-500 @enderror"
                                       placeholder="Leave empty for unlimited">
                                @error('max_members')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">Optional: Set a limit on group membership</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $group->is_active) ? 'checked' : '' }}
                                           class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                    <span class="ml-3 text-sm font-semibold text-gray-700">Active Group</span>
                                </label>
                                <p class="text-gray-500 text-sm mt-1">Inactive groups won't be visible to users</p>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Current Image -->
                            @if($group->image)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Current Image</label>
                                <div class="relative">
                                    <img src="{{ Storage::url($group->image) }}" 
                                         alt="{{ $group->name }}" 
                                         class="w-full h-48 object-cover rounded-xl border border-gray-200">
                                    <div class="absolute top-2 right-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Current
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Group Image -->
                            <div>
                                <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ $group->image ? 'Replace Image' : 'Group Image' }}
                                </label>
                                <div id="image-upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-purple-400 transition-colors duration-200">
                                    <div id="upload-placeholder" class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <span class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                                <span>{{ $group->image ? 'Upload new file' : 'Upload a file' }}</span>
                                            </span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                    </div>
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

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="6"
                                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('description') border-red-500 @enderror"
                                          placeholder="Describe the purpose and activities of this group...">{{ old('description', $group->description) }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">Help members understand what this group is about</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-8 mt-8 border-t border-gray-200">
                        <button type="submit" 
                                class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Group
                        </button>
                        <a href="{{ route('admin.community.groups.show', $group) }}" 
                           class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Group Statistics -->
            <div class="mt-8 bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Group Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $group->created_at->format('M d, Y') }}</div>
                        <div class="text-sm text-gray-600">Created</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $group->creator->name ?? 'Unknown' }}</div>
                        <div class="text-sm text-gray-600">Created By</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $group->updated_at->format('M d, Y') }}</div>
                        <div class="text-sm text-gray-600">Last Updated</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

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

    function hideImagePreview() {
        uploadPlaceholder.classList.remove('hidden');
        imagePreview.classList.add('hidden');
        uploadArea.classList.add('border-dashed');
        uploadArea.classList.remove('border-solid', 'border-purple-300');
        previewImage.src = '';
        imageInput.value = '';
    }

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB.');
            return;
        }

        showImagePreview(file);
    });

    removeImageBtn.addEventListener('click', function() {
        hideImagePreview();
    });

    changeImageBtn.addEventListener('click', function() {
        imageInput.click();
    });

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
        if (files.length <= 0) return;

        const file = files[0];

        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file.');
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB.');
            return;
        }

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        imageInput.files = dataTransfer.files;

        showImagePreview(file);
    });

    uploadArea.addEventListener('click', function(e) {
        if (!e.target.closest('#image-preview')) {
            imageInput.click();
        }
    });
});
</script>
@endsection
