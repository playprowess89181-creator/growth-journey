@extends('layouts.admin')

@section('title', 'Manage Vocabulary Words')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Manage Vocabulary Words
                </h1>
                <p class="mt-2 text-gray-600">Browse categories and manage their words.</p>
            </div>
            <a href="{{ route('admin.vocabulary.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-xl shadow-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto space-y-6">
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

        <div class="space-y-4">
            @forelse($categories as $category)
                @php
                    $en = $category->translations->firstWhere('language_code', 'en');
                    $title = $en?->title ?: 'Untitled (EN)';
                    $words = $category->words ?? collect();
                @endphp
                <details class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden" data-category>
                    <summary class="px-6 py-4 cursor-pointer flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="text-sm text-gray-500">Category #{{ $category->id }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $title }}</div>
                        </div>
                        <div class="inline-flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                {{ $words->count() }} Words
                            </span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </summary>
                    <div class="px-6 pb-6">
                        @if($words->isEmpty())
                            <div class="text-center text-gray-600 py-6">
                                No words found for this category.
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-xl border border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Word Name</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Language Indicator</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach($words as $word)
                                            @php
                                                $availableCodes = $word->translations
                                                    ->filter(fn($t) => trim((string) $t->word_text) !== '' || trim((string) $t->meaning_text) !== '')
                                                    ->pluck('language_code')
                                                    ->all();
                                            @endphp
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $word->word_key }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($languages as $code => $name)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold {{ in_array($code, $availableCodes, true) ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                                                {{ strtoupper($code) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <div class="inline-flex items-center gap-2">
                                                        <a href="{{ route('admin.vocabulary.words.show', $word) }}"
                                                           class="inline-flex items-center px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm">
                                                            View
                                                        </a>
                                                        <form method="POST" action="{{ route('admin.vocabulary.words.destroy', $word) }}" onsubmit="return confirm('Delete this word and its translations?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex items-center px-3 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow-sm">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </details>
            @empty
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-6 text-center text-gray-600">
                    No categories found.
                </div>
            @endforelse
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

    const detailBlocks = document.querySelectorAll('details[data-category]');
    detailBlocks.forEach((details) => {
        details.addEventListener('toggle', () => {
            if (!details.open) {
                return;
            }
            detailBlocks.forEach((other) => {
                if (other !== details) {
                    other.open = false;
                }
            });
        });
    });
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
