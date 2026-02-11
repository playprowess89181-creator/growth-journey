<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\ModuleTranslation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::query()
            ->with('translations')
            ->withCount('levels')
            ->orderBy('order')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.modules.index', [
            'modules' => $modules,
            'languages' => $this->languages(),
        ]);
    }

    public function create()
    {
        return view('admin.content.modules.create', [
            'languages' => $this->languages(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
            'translations' => ['nullable', 'array'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.en.title' => ['required', 'string', 'max:255'],
            'translations.en.description' => ['nullable', 'string'],
        ]);

        $this->validateUniqueEnglishTitle($data['translations']['en']['title'], null);

        $nextOrder = ((int) Module::max('order')) + 1;
        $module = Module::create([
            'order' => $nextOrder,
            'status' => $data['status'],
        ]);

        $this->upsertTranslations($module, $data['translations'] ?? []);

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module created successfully');
    }

    public function edit(Module $module)
    {
        $module->load([
            'translations',
            'levels' => function ($query) {
                $query->withCount('lessons');
            },
        ]);

        return view('admin.content.modules.edit', [
            'module' => $module,
            'languages' => $this->languages(),
        ]);
    }

    public function update(Request $request, Module $module)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
            'translations' => ['nullable', 'array'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.en.title' => ['required', 'string', 'max:255'],
            'translations.en.description' => ['nullable', 'string'],
        ]);

        $this->validateUniqueEnglishTitle($data['translations']['en']['title'], $module->id);

        $module->update([
            'status' => $data['status'],
        ]);

        $this->upsertTranslations($module, $data['translations'] ?? []);

        return redirect()
            ->route('admin.modules.edit', $module)
            ->with('success', 'Module updated successfully');
    }

    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module deleted successfully');
    }

    private function languages(): array
    {
        return [
            'en' => 'English',
            'tr' => 'Turkish',
            'de' => 'German',
            'it' => 'Italian',
            'ar' => 'Arabic',
            'bs' => 'Bosnian',
            'sq' => 'Albanian',
        ];
    }

    private function normalizeForUniqueness(string $value): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($value));

        return function_exists('mb_strtolower')
            ? mb_strtolower($normalized ?? '', 'UTF-8')
            : strtolower($normalized ?? '');
    }

    private function validateUniqueEnglishTitle(string $title, ?int $ignoreModuleId): void
    {
        $needle = $this->normalizeForUniqueness($title);

        $query = ModuleTranslation::query()
            ->where('language_code', 'en')
            ->select(['module_id', 'title']);

        if ($ignoreModuleId !== null) {
            $query->where('module_id', '!=', $ignoreModuleId);
        }

        $existing = $query->get();
        foreach ($existing as $row) {
            if ($this->normalizeForUniqueness((string) $row->title) === $needle) {
                throw ValidationException::withMessages([
                    'translations.en.title' => 'A module with this English title already exists.',
                ]);
            }
        }
    }

    private function upsertTranslations(Module $module, array $translations): void
    {
        foreach ($translations as $code => $payload) {
            if (! is_array($payload)) {
                continue;
            }

            $title = trim((string) ($payload['title'] ?? ''));
            $description = (string) ($payload['description'] ?? '');

            if ($code !== 'en' && $title === '' && trim($description) === '') {
                continue;
            }

            if ($code === 'en' && $title === '') {
                continue;
            }

            $module->translations()->updateOrCreate(
                ['language_code' => $code],
                ['title' => $title, 'description' => $description]
            );
        }
    }
}
