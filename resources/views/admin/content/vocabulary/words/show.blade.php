@extends('layouts.admin')

@section('title', 'Word Details')

@section('content')
@php
    $categoryTitle = optional($category?->translations->firstWhere('language_code', 'en'))->title ?: 'Untitled (EN)';
    $translations = $word->translations->keyBy('language_code');
@endphp
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 sm:p-6 lg:p-8">
    <div class="max-w-6xl mx-auto mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Word Details
                </h1>
                <p class="mt-2 text-gray-600">Category: {{ $categoryTitle }}</p>
            </div>
            <a href="{{ route('admin.vocabulary.words.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-xl shadow-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <div class="max-w-6xl mx-auto space-y-6">
        @if(session('success'))
            <div id="success-alert" class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 shadow-sm transition-all duration-500 ease-in-out">
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
            <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-sm text-gray-500">Word #{{ $word->id }}</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $word->word_key }}</div>
                </div>
                <form method="POST" action="{{ route('admin.vocabulary.words.destroy', $word) }}" onsubmit="return confirm('Delete this word and its translations?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow-sm">
                        Delete
                    </button>
                </form>
            </div>
            <form method="POST" action="{{ route('admin.vocabulary.words.update', $word) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Word Key <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="word_key"
                               value="{{ old('word_key', $word->word_key) }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('word_key')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow font-semibold">
                            Save Changes
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($languages as $code => $name)
                        @php
                            $t = $translations->get($code);
                            $wordText = old('translations.'.$code.'.word_text', $t?->word_text ?? '');
                            $meaningText = old('translations.'.$code.'.meaning_text', $t?->meaning_text ?? '');
                        @endphp
                        <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-3">
                            <div class="text-sm font-semibold text-gray-800">{{ $name }} ({{ strtoupper($code) }})</div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Word</label>
                                <input type="text"
                                       name="translations[{{ $code }}][word_text]"
                                       value="{{ $wordText }}"
                                       class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600">Meaning</label>
                                <textarea name="translations[{{ $code }}][meaning_text]"
                                          rows="2"
                                          class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $meaningText }}</textarea>
                            </div>
                        </div>
                    @endforeach
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
