@extends('layouts.admin')

@section('title', 'Edit Lesson')

@section('content')
@php
    $moduleTitle = $module->translations->firstWhere('language_code', 'en')?->title ?? 'Untitled (EN)';
    $translations = $lesson->translations->keyBy('language_code');
@endphp
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 sm:p-6 lg:p-8">
    <div class="max-w-5xl mx-auto mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    {{ $moduleTitle }} — Level {{ $levelNumber }} — Edit Lesson
                </h1>
                <p class="mt-2 text-gray-600">Update lesson structure and translations.</p>
            </div>
            <a href="{{ route('admin.levels.show', [$module, $level]) }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-xl shadow-lg hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-300 hover:shadow-xl">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <form method="POST" action="{{ route('admin.lessons.update', [$module, $level, $lesson]) }}" class="p-6 space-y-8">
                @csrf
                @method('PUT')

                <div class="flex flex-wrap gap-2">
                    @foreach($languages as $code => $name)
                        @php $t = $translations->get($code); @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $t && (trim($t->title) !== '' || trim($t->content) !== '') ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $name }} ({{ strtoupper($code) }})
                        </span>
                    @endforeach
                </div>

                <div x-data="{ tab: 'en' }" class="space-y-4">
                    <div class="flex flex-wrap gap-2">
                        @foreach($languages as $code => $name)
                            <button type="button"
                                    @click="tab='{{ $code }}'; window.initSummernoteForLang?.('{{ $code }}')"
                                    class="px-4 py-2 rounded-xl border text-sm font-semibold transition-all"
                                    :class="tab === '{{ $code }}' ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'">
                                {{ $name }} ({{ strtoupper($code) }})
                            </button>
                        @endforeach
                    </div>

                    @foreach($languages as $code => $name)
                        @php
                            $t = $translations->get($code);
                            $defaultTitle = $t?->title ?? '';
                            $defaultContent = $t?->content ?? '';
                        @endphp
                        <div x-show="tab === '{{ $code }}'" class="grid grid-cols-1 gap-6">
                            <div>
                                @if($code === 'en')
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Title *</label>
                                            <input type="text"
                                                   name="translations[en][title]"
                                                   value="{{ old('translations.en.title', $defaultTitle) }}"
                                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                   required>
                                            @error('translations.en.title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                                            <select name="status" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                <option value="active" {{ old('status', $lesson->status) === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status', $lesson->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Difficulty <span class="text-red-500">*</span></label>
                                            <select name="difficulty" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                                <option value="Beginner" {{ old('difficulty', $lesson->difficulty ?? 'Beginner') === 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                                <option value="Intermediate" {{ old('difficulty', $lesson->difficulty ?? 'Beginner') === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                                <option value="Advanced" {{ old('difficulty', $lesson->difficulty ?? 'Beginner') === 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                            </select>
                                            @error('difficulty')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                @else
                                    <label class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text"
                                           name="translations[{{ $code }}][title]"
                                           value="{{ old('translations.'.$code.'.title', $defaultTitle) }}"
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('translations.'.$code.'.title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Content {{ $code === 'en' ? '*' : '' }}</label>
                                <textarea data-lang="{{ $code }}"
                                          name="translations[{{ $code }}][content]"
                                          rows="10"
                                          class="summernote mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          {{ $code === 'en' ? 'required' : '' }}>{{ old('translations.'.$code.'.content', $defaultContent) }}</textarea>
                                @error('translations.'.$code.'.content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
    <style>
        .note-editor.note-frame {
            border: 1px solid #D1D5DB;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);
            background-color: rgba(255,255,255,0.8);
            backdrop-filter: blur(4px);
        }
        .note-editor.note-frame:focus-within {
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        }
        .note-toolbar {
            border-bottom: 1px solid #E5E7EB;
            background-color: #F9FAFB;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            padding: 0.5rem;
        }
        .note-editing-area .note-editable {
            min-height: 300px;
            padding: 1rem;
            font-family: 'Figtree', ui-sans-serif, system-ui, -apple-system, 'Segoe UI', 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji';
            color: #111827;
        }
        .note-editing-area .note-editable ul,
        .note-editing-area .note-editable ol {
            padding-left: 1.5rem;
            list-style-position: outside;
        }
        .note-editing-area .note-editable ul {
            list-style-type: disc;
        }
        .note-editing-area .note-editable ol {
            list-style-type: decimal;
        }
        .note-placeholder {
            color: #6B7280;
        }
        .note-statusbar {
            border-top: 1px solid #E5E7EB;
            background-color: #F9FAFB;
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
        }
        .note-btn {
            border-radius: 0.5rem;
            border-color: #E5E7EB;
        }
        .note-dropdown-menu {
            border-radius: 0.75rem;
            border-color: #E5E7EB;
        }
        .note-modal-backdrop { background-color: rgba(0,0,0,.3); backdrop-filter: blur(2px); }
        .note-modal .note-modal-content,
        .note-dialog .note-dialog-content {
            border: 1px solid #E5E7EB;
            border-radius: 1rem;
            background-color: #ffffff;
            box-shadow: 0 10px 25px rgba(31, 41, 55, 0.15);
        }
        .note-modal .note-modal-header,
        .note-dialog .note-dialog-header {
            border: none;
            border-bottom: 1px solid #E5E7EB;
            padding: 0.75rem 1rem;
        }
        .note-modal .note-modal-title,
        .note-dialog .note-dialog-title {
            font-size: 1rem;
            font-weight: 600;
            color: #111827; /* gray-900 */
        }
        .note-modal .note-modal-body,
        .note-dialog .note-dialog-body {
            padding: 1rem;
        }
        .note-modal .note-modal-footer,
        .note-dialog .note-dialog-footer {
            border-top: 1px solid #E5E7EB;
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            height: auto;
        }
        .note-modal input[type="text"],
        .note-modal input[type="url"],
        .note-modal input[type="file"],
        .note-modal textarea,
        .note-dialog input[type="text"],
        .note-dialog input[type="url"],
        .note-dialog input[type="file"],
        .note-dialog textarea {
            width: 100%;
            border: 1px solid #D1D5DB;
            border-radius: 0.75rem;
            padding: 0.5rem 0.75rem;
            color: #111827;
        }
        .note-modal input:focus,
        .note-modal textarea:focus,
        .note-dialog input:focus,
        .note-dialog textarea:focus {
            outline: none;
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        }
        .note-modal .note-btn,
        .note-dialog .note-btn {
            border-radius: 0.5rem;
            border: 1px solid #E5E7EB;
        }
        .note-modal .note-btn-primary,
        .note-dialog .note-btn-primary {
            background-color: #4F46E5;
            border-color: #4F46E5;
            color: #ffffff;
        }
        .note-modal .note-btn-primary:hover,
        .note-dialog .note-btn-primary:hover { background-color: #4338CA; }
        .note-popover {
            border: 1px solid #E5E7EB;
            border-radius: 0.75rem;
            background-color: #ffffff;
            box-shadow: 0 10px 25px rgba(31, 41, 55, 0.15);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
    <script>
        $(function () {
            function initEditor($editor) {
                if ($editor.data('summernote-initialized')) {
                    return;
                }

                $editor.summernote({
                height: 350,
                placeholder: 'Enter lesson content...',
                dialogsInBody: true,
                styleTags: ['p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
                fontNames: ['Figtree', 'Arial', 'Helvetica', 'Times New Roman', 'Courier New', 'Georgia'],
                fontNamesIgnoreCheck: ['Figtree'],
                fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '24', '36', '48', '64'],
                lineHeights: ['0.7', '1.0', '1.4', '1.8', '2.0', '2.4'],
                toolbar: [
                    ['style', ['style']],
                    ['font', ['fontname', 'fontsize', 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph', 'lineHeight']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['codeview', 'help']]
                ],
                callbacks: {
                    onChange: function(contents) {
                        $editor.val(contents);
                    }
                }
                });

                $editor.data('summernote-initialized', true);
            }

            window.initSummernoteForLang = function (lang) {
                initEditor($('textarea.summernote[data-lang="' + lang + '"]'));
            };

            initEditor($('textarea.summernote[data-lang="en"]'));

            $('form').on('submit', function () {
                $('textarea.summernote').each(function () {
                    const $el = $(this);
                    if ($el.data('summernote-initialized')) {
                        $el.val($el.summernote('code'));
                    }
                });
            });
        });
    </script>
@endpush
