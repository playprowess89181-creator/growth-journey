@extends('layouts.admin')

@section('title', 'Edit Module')

@section('content')
@php
    $translations = $module->translations->keyBy('language_code');
    $initialHasTranslation = [];
    foreach ($languages as $code => $name) {
        $t = $translations->get($code);
        $title = trim((string) old('translations.'.$code.'.title', $t?->title ?? ''));
        $description = trim((string) old('translations.'.$code.'.description', $t?->description ?? ''));
        $initialHasTranslation[$code] = $title !== '' || $description !== '';
    }
@endphp
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 sm:p-6 lg:p-8">
    <div class="max-w-6xl mx-auto mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Edit Module
                </h1>
                <p class="mt-2 text-gray-600">Manage module structure and translations.</p>
            </div>
            <a href="{{ route('admin.modules.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-xl shadow-lg hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <div class="max-w-6xl mx-auto space-y-8">

        <!-- Success Alert -->
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
            <form method="POST" action="{{ route('admin.modules.update', $module) }}" class="p-6 space-y-8">
                @csrf
                @method('PUT')

                <div x-data="{
                        tab: 'en',
                        has: @js($initialHasTranslation),
                        updateHas(code) {
                            const title = this.$root.querySelector(`[name='translations[${code}][title]']`);
                            const description = this.$root.querySelector(`[name='translations[${code}][description]']`);
                            const t = title ? title.value.trim() : '';
                            const d = description ? description.value.trim() : '';
                            this.has[code] = t !== '' || d !== '';
                        }
                    }"
                    class="space-y-6">
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

                    @foreach($languages as $code => $name)
                        @php
                            $t = $translations->get($code);
                            $defaultTitle = $t?->title ?? '';
                            $defaultDescription = $t?->description ?? '';
                        @endphp
                        <div x-show="tab === '{{ $code }}'" class="grid grid-cols-1 gap-6">
                            @if($code === 'en')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                                        <input type="text"
                                               name="translations[en][title]"
                                               value="{{ old('translations.en.title', $defaultTitle) }}"
                                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                               required
                                               @input="updateHas('en')">
                                        @error('translations.en.title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                                        <select name="status" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                            <option value="active" {{ old('status', $module->status) === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $module->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            @else
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text"
                                           name="translations[{{ $code }}][title]"
                                           value="{{ old('translations.'.$code.'.title', $defaultTitle) }}"
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           @input="updateHas('{{ $code }}')">
                                    @error('translations.'.$code.'.title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            @endif
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="translations[{{ $code }}][description]"
                                          rows="4"
                                          class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          @input="updateHas('{{ $code }}')">{{ old('translations.'.$code.'.description', $defaultDescription) }}</textarea>
                                @error('translations.'.$code.'.description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow">Save Changes</button>
                </div>
            </form>
        </div>

        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Levels</h2>
                <form method="POST" action="{{ route('admin.levels.store', $module) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow">
                        <i class="fas fa-plus mr-2"></i>
                        Add Level
                    </button>
                </form>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Level</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lessons</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($module->levels->sortBy('id') as $level)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">Level {{ $loop->iteration }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.levels.update', [$module, $level]) }}" class="flex items-center gap-3">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                <option value="active" {{ $level->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $level->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            <button type="submit" class="inline-flex items-center px-3 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm text-sm font-semibold">
                                                Save
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $level->lessons_count }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.levels.show', [$module, $level]) }}" class="inline-flex items-center px-3 py-2 rounded-xl bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold shadow-sm">
                                                Manage Lessons
                                            </a>
                                            <form method="POST" action="{{ route('admin.levels.destroy', [$module, $level]) }}" onsubmit="return confirm('Delete this level and its lessons?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold shadow-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-gray-600">No levels yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
// Auto-dismiss alert functionality
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
