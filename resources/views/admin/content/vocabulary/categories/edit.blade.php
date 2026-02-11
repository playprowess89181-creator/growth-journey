@extends('layouts.admin')

@section('title', 'Edit Vocabulary Category')

@section('content')
@php
    $initialHasTranslation = [];
    foreach ($languages as $code => $name) {
        $currentTitle = (string) optional($category->translations->firstWhere('language_code', $code))->title;
        $oldTitle = (string) old('translations.'.$code.'.title', $currentTitle);
        $initialHasTranslation[$code] = trim($oldTitle) !== '';
    }
@endphp
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 sm:p-6 lg:p-8">
    <div class="max-w-5xl mx-auto mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Edit Vocabulary Category
                </h1>
                <p class="mt-2 text-gray-600">Update the category titles and status.</p>
            </div>
            <a href="{{ route('admin.vocabulary.categories.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-xl shadow-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <div class="max-w-5xl mx-auto">
        @if(session('success'))
            <div id="success-alert" class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 shadow-sm transition-all duration-500 ease-in-out">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                    <div class="ml-auto">
                        <button type="button" class="text-green-400 hover:text-green-600 transition-colors duration-200" onclick="dismissAlert('success-alert')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <form method="POST" action="{{ route('admin.vocabulary.categories.update', $category) }}" class="p-6 space-y-8">
                @csrf
                @method('PUT')

                <div x-data="{
                        tab: 'en',
                        has: @js($initialHasTranslation),
                        updateHas(code) {
                            const title = this.$root.querySelector(`[name='translations[${code}][title]']`);
                            const t = title ? title.value.trim() : '';
                            this.has[code] = t !== '';
                        }
                    }"
                    class="space-y-6">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($languages as $code => $name)
                                <button type="button"
                                        @click="tab='{{ $code }}'"
                                        class="px-4 py-2 rounded-xl border text-sm font-semibold transition-all"
                                        :class="tab === '{{ $code }}' ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full"
                                              :class="{{ $code === 'en' ? "has['en'] ? 'bg-emerald-500' : 'bg-amber-400'" : "has['$code'] ? 'bg-emerald-500' : 'bg-gray-300'" }}"></span>
                                        {{ $name }} ({{ strtoupper($code) }})
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @foreach($languages as $code => $name)
                        @php
                            $currentTitle = (string) optional($category->translations->firstWhere('language_code', $code))->title;
                            $value = old('translations.'.$code.'.title', $currentTitle);
                        @endphp
                        <div x-show="tab === '{{ $code }}'" class="grid grid-cols-1 gap-6">
                            @if($code === 'en')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                                        <input type="text"
                                               name="translations[en][title]"
                                               value="{{ $value }}"
                                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               required
                                               @input="updateHas('en')">
                                        @error('translations.en.title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                                        <select name="is_active" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                            <option value="1" {{ old('is_active', $category->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('is_active', $category->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('is_active')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            @else
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text"
                                           name="translations[{{ $code }}][title]"
                                           value="{{ $value }}"
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           @input="updateHas('{{ $code }}')">
                                    @error('translations.'.$code.'.title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.vocabulary.categories.index') }}" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            dismissAlert('success-alert');
        }, 5000);
    }
});

function dismissAlert(alertId) {
    const alert = document.getElementById(alertId);
    if (alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            alert.remove();
        }, 500);
    }
}
</script>
@endsection
