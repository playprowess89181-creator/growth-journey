<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VCategory;
use App\Services\VocabularyImportService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VocabularyUploadController extends Controller
{
    public function index()
    {
        $categories = VCategory::query()
            ->with('translations')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.content.vocabulary.upload-words.index', [
            'categories' => $categories,
            'languages' => $this->languages(),
            'preview' => null,
            'previewToken' => null,
            'selectedCategoryId' => null,
        ]);
    }

    public function preview(Request $request, VocabularyImportService $importer)
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:v_categories,id'],
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:20480'],
        ]);

        $categories = VCategory::query()
            ->with('translations')
            ->orderByDesc('created_at')
            ->get();

        $languages = $this->languages();
        $languageCodes = array_keys($languages);

        $existingPreview = $request->session()->get('vocabulary_upload_preview');
        if (is_array($existingPreview) && isset($existingPreview['path'])) {
            Storage::disk('local')->delete($existingPreview['path']);
        }

        $token = (string) Str::uuid();
        $file = $request->file('file');
        $extension = $file?->getClientOriginalExtension() ?: 'csv';
        $storedPath = $file?->storeAs('vocabulary-previews', $token.'.'.$extension);

        $preview = $file ? $importer->previewFile($file, $languageCodes, 10) : null;

        $request->session()->put('vocabulary_upload_preview', [
            'token' => $token,
            'path' => $storedPath,
            'original_name' => $file?->getClientOriginalName(),
            'category_id' => (int) $data['category_id'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'preview' => $preview,
                'previewToken' => $token,
                'selectedCategoryId' => (int) $data['category_id'],
            ]);
        }

        return view('admin.content.vocabulary.upload-words.index', [
            'categories' => $categories,
            'languages' => $languages,
            'preview' => $preview,
            'previewToken' => $token,
            'selectedCategoryId' => (int) $data['category_id'],
        ]);
    }

    public function store(Request $request, VocabularyImportService $importer)
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:v_categories,id'],
            'preview_token' => ['required', 'string'],
        ]);

        $categoryId = (int) $data['category_id'];
        $languageCodes = array_keys($this->languages());
        $preview = $request->session()->get('vocabulary_upload_preview');

        if (! is_array($preview) || ($preview['token'] ?? null) !== $data['preview_token']) {
            return redirect()
                ->route('admin.vocabulary.upload-words.index')
                ->withErrors(['file' => 'Please preview the file before uploading.']);
        }

        if (($preview['category_id'] ?? null) !== $categoryId) {
            return redirect()
                ->route('admin.vocabulary.upload-words.index')
                ->withErrors(['category_id' => 'The selected category does not match the previewed file.']);
        }

        $path = $preview['path'] ?? null;
        if (! is_string($path) || ! Storage::disk('local')->exists($path)) {
            return redirect()
                ->route('admin.vocabulary.upload-words.index')
                ->withErrors(['file' => 'Preview file is missing. Please preview again.']);
        }

        $fullPath = Storage::disk('local')->path($path);
        $uploadedFile = new UploadedFile(
            $fullPath,
            $preview['original_name'] ?? basename($fullPath),
            null,
            null,
            true
        );

        $summary = $importer->importCategoryWords(
            $uploadedFile,
            $categoryId,
            $languageCodes,
        );

        $failedCount = count($summary['failed'] ?? []);
        $message = $failedCount === 0
            ? 'Upload completed successfully.'
            : 'Upload completed with '.$failedCount.' failed rows.';

        Storage::disk('local')->delete($path);
        $request->session()->forget('vocabulary_upload_preview');

        return redirect()
            ->route('admin.vocabulary.upload-words.index')
            ->with('success', $message)
            ->with('import_summary', $summary + ['category_id' => $categoryId]);
    }

    public function template(): StreamedResponse
    {
        $languageCodes = array_keys($this->languages());
        $headers = ['word_key'];
        foreach ($languageCodes as $code) {
            $headers[] = $code.'_word';
            $headers[] = $code.'_meaning';
        }

        $filename = 'vocabulary_upload_template.csv';

        return response()->streamDownload(function () use ($headers) {
            echo "\xEF\xBB\xBF";
            $out = fopen('php://output', 'w');
            if (! is_resource($out)) {
                return;
            }

            fputcsv($out, $headers);
            $example = array_fill(0, count($headers), '');
            $example[0] = 'example_key';
            fputcsv($out, $example);

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function languages(): array
    {
        return [
            'en' => 'English',
            'tr' => 'Turkish',
            'de' => 'German',
            'ar' => 'Arabic',
            'bs' => 'Bosnian',
            'sq' => 'Albanian',
            'it' => 'Italian',
        ];
    }
}
