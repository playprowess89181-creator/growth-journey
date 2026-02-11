@extends('layouts.admin')

@section('title', 'Edit Prayer Request')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Prayer Request</h1>
                <p class="text-gray-600">Update request details and moderation settings</p>
            </div>
            <a href="{{ route('admin.prayer-wall.requests.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                Back
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-2xl px-6 py-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <form method="POST" action="{{ route('admin.prayer-wall.requests.update', $request) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="title">Title</label>
                    <input id="title"
                           name="title"
                           value="{{ old('title', $request->title) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                           required>
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="description">Description</label>
                    <textarea id="description"
                              name="description"
                              rows="6"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                              required>{{ old('description', $request->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="category">Category</label>
                        <input id="category"
                               name="category"
                               value="{{ old('category', $request->category) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                               required>
                        @error('category')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="is_public">Visibility</label>
                        <select id="is_public"
                                name="is_public"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                required>
                            <option value="1" {{ (string) old('is_public', (int) $request->is_public) === '1' ? 'selected' : '' }}>Public</option>
                            <option value="0" {{ (string) old('is_public', (int) $request->is_public) === '0' ? 'selected' : '' }}>Private</option>
                        </select>
                        @error('is_public')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="status">Status</label>
                        <select id="status"
                                name="status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                required>
                            <option value="active" {{ old('status', $request->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $request->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="archived" {{ old('status', $request->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="text-sm text-gray-500">
                        <span>Owner: {{ $request->user?->name ?? 'Unknown' }}</span>
                        <span class="mx-2">•</span>
                        <span>Created: {{ $request->created_at->toDayDateTimeString() }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.prayer-wall.requests.index') }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

