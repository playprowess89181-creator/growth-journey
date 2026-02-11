<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VCategory;
use Illuminate\Http\Request;

class VocabularyCategoryController extends Controller
{
    public function index()
    {
        $categories = VCategory::query()
            ->with('translations')
            ->withCount('words')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.content.vocabulary.categories.index', [
            'categories' => $categories,
            'languages' => $this->languages(),
        ]);
    }

    public function create()
    {
        return view('admin.content.vocabulary.categories.create', [
            'languages' => $this->languages(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'translations' => ['nullable', 'array'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.en.title' => ['required', 'string', 'max:255'],
        ]);

        $category = VCategory::create([
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->upsertTranslations($category, $data['translations'] ?? []);

        return redirect()
            ->route('admin.vocabulary.categories.index')
            ->with('success', 'Category created successfully');
    }

    public function edit(VCategory $category)
    {
        $category->load('translations');

        return view('admin.content.vocabulary.categories.edit', [
            'category' => $category,
            'languages' => $this->languages(),
        ]);
    }

    public function update(Request $request, VCategory $category)
    {
        $data = $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'translations' => ['nullable', 'array'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.en.title' => ['required', 'string', 'max:255'],
        ]);

        $category->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->upsertTranslations($category, $data['translations'] ?? []);

        return redirect()
            ->route('admin.vocabulary.categories.edit', $category)
            ->with('success', 'Category updated successfully');
    }

    public function destroy(VCategory $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.vocabulary.categories.index')
            ->with('success', 'Category deleted successfully');
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

    private function upsertTranslations(VCategory $category, array $translations): void
    {
        $supported = array_keys($this->languages());

        foreach ($translations as $code => $payload) {
            if (! is_array($payload)) {
                continue;
            }

            $code = strtolower(trim((string) $code));
            if (! in_array($code, $supported, true)) {
                continue;
            }

            $title = trim((string) ($payload['title'] ?? ''));

            if ($code !== 'en' && $title === '') {
                continue;
            }

            if ($code === 'en' && $title === '') {
                continue;
            }

            $category->translations()->updateOrCreate(
                ['language_code' => $code],
                ['title' => $title],
            );
        }
    }
}
