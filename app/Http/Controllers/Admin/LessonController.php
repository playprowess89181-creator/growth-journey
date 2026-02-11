<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Level;
use App\Models\Module;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function create(Module $module, Level $level)
    {
        $this->ensureLevelBelongsToModule($module, $level);
        $levelNumber = $this->levelNumber($module, $level);

        return view('admin.content.lessons.create', [
            'module' => $module->load('translations'),
            'level' => $level,
            'levelNumber' => $levelNumber,
            'languages' => $this->languages(),
        ]);
    }

    public function store(Request $request, Module $module, Level $level)
    {
        $this->ensureLevelBelongsToModule($module, $level);

        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
            'difficulty' => ['required', 'in:Beginner,Intermediate,Advanced'],
            'translations' => ['required', 'array'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.content' => ['nullable', 'string'],
            'translations.en.title' => ['required', 'string', 'max:255'],
            'translations.en.content' => ['required', 'string'],
        ]);

        $order = ((int) $level->lessons()->max('order')) + 1;

        $lesson = $level->lessons()->create([
            'order' => $order,
            'status' => $data['status'],
            'difficulty' => $data['difficulty'],
        ]);

        $this->upsertTranslations($lesson, $data['translations']);

        return redirect()
            ->route('admin.levels.show', [$module, $level])
            ->with('success', 'Lesson created successfully');
    }

    public function edit(Module $module, Level $level, Lesson $lesson)
    {
        $this->ensureLessonBelongsToLevel($module, $level, $lesson);

        $lesson->load('translations');
        $levelNumber = $this->levelNumber($module, $level);

        return view('admin.content.lessons.edit', [
            'module' => $module->load('translations'),
            'level' => $level,
            'levelNumber' => $levelNumber,
            'lesson' => $lesson,
            'languages' => $this->languages(),
        ]);
    }

    public function update(Request $request, Module $module, Level $level, Lesson $lesson)
    {
        $this->ensureLessonBelongsToLevel($module, $level, $lesson);

        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
            'difficulty' => ['required', 'in:Beginner,Intermediate,Advanced'],
            'translations' => ['required', 'array'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.content' => ['nullable', 'string'],
            'translations.en.title' => ['required', 'string', 'max:255'],
            'translations.en.content' => ['required', 'string'],
        ]);

        $lesson->update([
            'status' => $data['status'],
            'difficulty' => $data['difficulty'],
        ]);

        $this->upsertTranslations($lesson, $data['translations']);

        return redirect()
            ->route('admin.levels.show', [$module, $level])
            ->with('success', 'Lesson updated successfully');
    }

    public function destroy(Module $module, Level $level, Lesson $lesson)
    {
        $this->ensureLessonBelongsToLevel($module, $level, $lesson);

        $lesson->delete();

        return redirect()
            ->route('admin.levels.show', [$module, $level])
            ->with('success', 'Lesson deleted successfully');
    }

    private function ensureLevelBelongsToModule(Module $module, Level $level): void
    {
        if ((int) $level->module_id !== (int) $module->id) {
            abort(404);
        }
    }

    private function ensureLessonBelongsToLevel(Module $module, Level $level, Lesson $lesson): void
    {
        $this->ensureLevelBelongsToModule($module, $level);

        if ((int) $lesson->level_id !== (int) $level->id) {
            abort(404);
        }
    }

    private function levelNumber(Module $module, Level $level): int
    {
        $levelIds = Level::query()
            ->where('module_id', $module->id)
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $index = array_search((int) $level->id, array_map('intval', $levelIds), true);

        return $index === false ? 1 : $index + 1;
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

    private function upsertTranslations(Lesson $lesson, array $translations): void
    {
        foreach ($translations as $code => $payload) {
            if (! is_array($payload)) {
                continue;
            }

            $title = trim((string) ($payload['title'] ?? ''));
            $content = (string) ($payload['content'] ?? '');

            if ($code !== 'en' && $title === '' && trim($content) === '') {
                continue;
            }

            if ($code === 'en' && ($title === '' || trim($content) === '')) {
                continue;
            }

            $lesson->translations()->updateOrCreate(
                ['language_code' => $code],
                ['title' => $title, 'content' => $content]
            );
        }
    }
}
