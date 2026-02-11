@extends('layouts.admin')

@section('title', 'Upload Vocabulary Words')

@section('content')
@php
    $headers = ['word_key'];
    foreach (array_keys($languages) as $code) {
        $headers[] = $code.'_word';
        $headers[] = $code.'_meaning';
    }
    $selectedCategoryId = $selectedCategoryId ?? old('category_id');
@endphp
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Upload Vocabulary Words
                </h1>
                <p class="mt-2 text-gray-600">Bulk import words per category using CSV or XLSX.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.vocabulary.index') }}"
                   class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold rounded-xl shadow-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
                <a href="{{ route('admin.vocabulary.upload-words.template') }}"
                   class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl shadow-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-300">
                    <i class="fas fa-download mr-2"></i>
                    Download Template
                </a>
                <a href="{{ route('admin.vocabulary.categories.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-300">
                    <i class="fas fa-layer-group mr-2"></i>
                    Categories
                </a>
            </div>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <form method="POST" action="{{ route('admin.vocabulary.upload-words.preview') }}" enctype="multipart/form-data" class="p-6 space-y-6" id="preview-form">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Select a category</option>
                            @foreach($categories as $category)
                                @php
                                    $en = $category->translations->firstWhere('language_code', 'en');
                                    $title = $en?->title ?: 'Untitled (EN)';
                                @endphp
                                <option value="{{ $category->id }}" {{ (string) $selectedCategoryId === (string) $category->id ? 'selected' : '' }}>
                                    {{ $title }} (ID: {{ $category->id }})
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Upload File (CSV / XLSX) <span class="text-red-500">*</span></label>
                        <div class="mt-2">
                            <label for="file" class="group flex flex-col gap-3 rounded-2xl border border-dashed border-indigo-300 bg-gradient-to-br from-indigo-50 via-white to-purple-50 px-5 py-6 shadow-sm transition hover:border-indigo-400 hover:shadow-md cursor-pointer">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg">
                                            <i class="fas fa-cloud-upload-alt text-lg"></i>
                                        </span>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800">Choose a CSV or XLSX file</div>
                                            <div class="text-xs text-gray-500">Drag and drop or click to browse</div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Required</span>
                                </div>
                                <div class="rounded-xl bg-white px-4 py-3 text-sm text-gray-600 shadow-inner" id="file-name">No file selected</div>
                            </label>
                            <input id="file"
                                   type="file"
                                   name="file"
                                   accept=".csv,.txt,.xlsx"
                                   class="sr-only"
                                   required>
                        </div>
                        @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="text-sm text-gray-600">
                            Step 1: Preview the file before uploading
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <button type="submit" id="preview-button" class="px-4 py-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                                Preview
                            </button>
                            <div id="upload-container">
                                @if(is_array($preview) && ($preview['header_valid'] ?? false))
                                    <form method="POST" action="{{ route('admin.vocabulary.upload-words.store') }}">
                                        @csrf
                                        <input type="hidden" name="category_id" value="{{ $selectedCategoryId }}">
                                        <input type="hidden" name="preview_token" value="{{ $previewToken }}">
                                        <button type="submit" class="px-4 py-3 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow font-semibold">
                                            Upload
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <div class="p-6">
                    <div class="text-lg font-bold text-gray-900">Required File Format</div>
                    <div class="mt-2 text-sm text-gray-600">Header row must match exactly:</div>
                    <div class="mt-3 p-3 bg-gray-900 text-gray-100 rounded-xl text-xs overflow-x-auto whitespace-nowrap">
                        {{ implode(', ', $headers) }}
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        <div class="font-semibold text-gray-800">Rules</div>
                        <ul class="mt-2 space-y-2 text-sm">
                            <li class="flex gap-2"><i class="fas fa-check text-emerald-600 mt-1"></i><span><span class="font-semibold">word_key</span> is required.</span></li>
                            <li class="flex gap-2"><i class="fas fa-check text-emerald-600 mt-1"></i><span>Language columns may be empty.</span></li>
                            <li class="flex gap-2"><i class="fas fa-check text-emerald-600 mt-1"></i><span>Re-uploading the same <span class="font-semibold">word_key</span> updates translations.</span></li>
                            <li class="flex gap-2"><i class="fas fa-check text-emerald-600 mt-1"></i><span>Invalid rows are skipped with error reporting.</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="preview-section" class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <div class="text-lg font-bold text-gray-900">Preview</div>
                <div class="text-sm text-gray-600">
                    Total rows detected: <span id="preview-total" class="font-semibold text-gray-900">{{ (int) ($preview['total_rows'] ?? 0) }}</span>
                </div>
            </div>
            <div id="preview-content" class="p-6 space-y-4">
                @if(is_array($preview))
                    @if(!($preview['header_valid'] ?? false))
                        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            {{ $preview['errors'][0] ?? 'Invalid file format.' }}
                        </div>
                    @else
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            Header looks good. Preview the first rows below, then upload.
                        </div>
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @foreach($preview['expected_headers'] ?? [] as $header)
                                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ $header }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse($preview['rows'] ?? [] as $row)
                                        <tr>
                                            @foreach($preview['expected_headers'] ?? [] as $header)
                                                <td class="px-4 py-2 text-sm text-gray-700">{{ $row[$header] ?? '' }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($preview['expected_headers'] ?? []) }}" class="px-4 py-4 text-center text-sm text-gray-500">
                                                No data rows found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-6 text-sm text-gray-500 text-center">
                        No preview yet.
                    </div>
                @endif
            </div>
        </div>

        @php
            $summary = session('import_summary');
            $failed = is_array($summary) ? ($summary['failed'] ?? []) : [];
        @endphp

        @if(is_array($summary))
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-wrap gap-3 items-center justify-between">
                        <div class="text-lg font-bold text-gray-900">Import Summary</div>
                        <div class="text-sm text-gray-600">
                            Processed: <span class="font-semibold text-gray-900">{{ (int) ($summary['processed'] ?? 0) }}</span>,
                            Created: <span class="font-semibold text-gray-900">{{ (int) ($summary['created'] ?? 0) }}</span>,
                            Updated: <span class="font-semibold text-gray-900">{{ (int) ($summary['updated'] ?? 0) }}</span>,
                            Failed: <span class="font-semibold text-gray-900">{{ count($failed) }}</span>
                        </div>
                    </div>

                    @if(count($failed) > 0)
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Row</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Word Key</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Error</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($failed as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item['row'] ?? '' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item['word_key'] ?? '' }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $item['error'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif
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

    const fileInput = document.getElementById('file');
    const fileName = document.getElementById('file-name');
    const previewButton = document.getElementById('preview-button');
    const categorySelect = document.querySelector('select[name="category_id"]');
    const previewForm = document.getElementById('preview-form');
    const previewContent = document.getElementById('preview-content');
    const previewTotal = document.getElementById('preview-total');
    const uploadContainer = document.getElementById('upload-container');
    const csrfToken = '{{ csrf_token() }}';

    function updatePreviewState() {
        const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
        const hasCategory = categorySelect && categorySelect.value !== '';
        if (previewButton) {
            previewButton.disabled = !hasFile || !hasCategory;
        }
    }

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (fileName) {
                fileName.textContent = this.files && this.files[0] ? this.files[0].name : 'No file selected';
            }
            updatePreviewState();
        });
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', updatePreviewState);
    }

    updatePreviewState();

    function clearPreviewContent() {
        if (previewContent) {
            previewContent.innerHTML = '';
        }
    }

    function renderPreviewError(message) {
        if (previewTotal) {
            previewTotal.textContent = '0';
        }
        clearPreviewContent();
        if (previewContent) {
            const error = document.createElement('div');
            error.className = 'rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700';
            error.textContent = message || 'Unable to preview this file.';
            previewContent.appendChild(error);
        }
        if (uploadContainer) {
            uploadContainer.innerHTML = '';
        }
    }

    function renderUploadButton(previewToken, categoryId) {
        if (!uploadContainer || !previewToken || !categoryId) {
            if (uploadContainer) {
                uploadContainer.innerHTML = '';
            }
            return;
        }
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.vocabulary.upload-words.store') }}';

        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;

        const categoryInput = document.createElement('input');
        categoryInput.type = 'hidden';
        categoryInput.name = 'category_id';
        categoryInput.value = categoryId;

        const previewInput = document.createElement('input');
        previewInput.type = 'hidden';
        previewInput.name = 'preview_token';
        previewInput.value = previewToken;

        const button = document.createElement('button');
        button.type = 'submit';
        button.className = 'px-4 py-3 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow font-semibold';
        button.textContent = 'Upload';

        form.appendChild(tokenInput);
        form.appendChild(categoryInput);
        form.appendChild(previewInput);
        form.appendChild(button);

        uploadContainer.innerHTML = '';
        uploadContainer.appendChild(form);
    }

    function renderPreviewTable(headers, rows) {
        const tableWrap = document.createElement('div');
        tableWrap.className = 'overflow-x-auto rounded-xl border border-gray-200';

        const table = document.createElement('table');
        table.className = 'min-w-full divide-y divide-gray-200 text-sm';

        const thead = document.createElement('thead');
        thead.className = 'bg-gray-50';
        const headRow = document.createElement('tr');
        headers.forEach((header) => {
            const th = document.createElement('th');
            th.className = 'px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider';
            th.textContent = header;
            headRow.appendChild(th);
        });
        thead.appendChild(headRow);

        const tbody = document.createElement('tbody');
        tbody.className = 'bg-white divide-y divide-gray-100';

        if (rows.length === 0) {
            const emptyRow = document.createElement('tr');
            const emptyCell = document.createElement('td');
            emptyCell.colSpan = headers.length || 1;
            emptyCell.className = 'px-4 py-4 text-center text-sm text-gray-500';
            emptyCell.textContent = 'No data rows found.';
            emptyRow.appendChild(emptyCell);
            tbody.appendChild(emptyRow);
        } else {
            rows.forEach((row) => {
                const tr = document.createElement('tr');
                headers.forEach((header) => {
                    const td = document.createElement('td');
                    td.className = 'px-4 py-2 text-sm text-gray-700';
                    td.textContent = row && Object.prototype.hasOwnProperty.call(row, header) ? row[header] : '';
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            });
        }

        table.appendChild(thead);
        table.appendChild(tbody);
        tableWrap.appendChild(table);
        return tableWrap;
    }

    function renderPreview(preview, previewToken, categoryId) {
        if (!preview || typeof preview !== 'object') {
            renderPreviewError('Invalid preview response.');
            return;
        }
        if (previewTotal) {
            previewTotal.textContent = String(preview.total_rows ?? 0);
        }
        clearPreviewContent();

        if (preview.header_valid === false) {
            const error = document.createElement('div');
            error.className = 'rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700';
            const errorMessage = Array.isArray(preview.errors) && preview.errors[0] ? preview.errors[0] : 'Invalid file format.';
            error.textContent = errorMessage;
            if (previewContent) {
                previewContent.appendChild(error);
            }
            renderUploadButton(null, null);
            return;
        }

        const info = document.createElement('div');
        info.className = 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700';
        info.textContent = 'Header looks good. Preview the first rows below, then upload.';
        if (previewContent) {
            previewContent.appendChild(info);
        }

        const headers = Array.isArray(preview.expected_headers) ? preview.expected_headers : [];
        const rows = Array.isArray(preview.rows) ? preview.rows : [];
        const table = renderPreviewTable(headers, rows);
        if (previewContent) {
            previewContent.appendChild(table);
        }

        renderUploadButton(previewToken, categoryId);
    }

    if (previewForm) {
        previewForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            if (!previewButton || previewButton.disabled) {
                return;
            }
            const originalText = previewButton.textContent;
            previewButton.disabled = true;
            previewButton.textContent = 'Previewing...';

            try {
                const formData = new FormData(previewForm);
                const response = await fetch(previewForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (response.status === 422) {
                    const errorData = await response.json();
                    const errorMessages = Object.values(errorData.errors || {}).flat();
                    renderPreviewError(errorMessages.join(' ') || 'Please check the form and try again.');
                    return;
                }

                if (!response.ok) {
                    renderPreviewError('Unable to preview this file.');
                    return;
                }

                const data = await response.json();
                renderPreview(data.preview, data.previewToken, data.selectedCategoryId);
            } catch (error) {
                renderPreviewError('Unable to preview this file.');
            } finally {
                previewButton.textContent = originalText;
                updatePreviewState();
            }
        });
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
