<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Module;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    public function show(Module $module, Level $level)
    {
        $this->ensureLevelBelongsToModule($module, $level);

        $level->load(['module', 'lessons.translations']);

        $levelIds = Level::query()
            ->where('module_id', $module->id)
            ->orderBy('id')
            ->pluck('id')
            ->all();
        $index = array_search((int) $level->id, array_map('intval', $levelIds), true);
        $levelNumber = $index === false ? 1 : $index + 1;

        return view('admin.levels.show', [
            'module' => $module->load('translations'),
            'level' => $level,
            'levelNumber' => $levelNumber,
            'languages' => $this->languages(),
        ]);
    }

    public function store(Module $module)
    {
        $module->levels()->create([
            'status' => 'active',
        ]);

        return redirect()
            ->route('admin.modules.edit', $module)
            ->with('success', 'Level added successfully');
    }

    public function update(Request $request, Module $module, Level $level)
    {
        $this->ensureLevelBelongsToModule($module, $level);

        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $level->update($data);

        return redirect()
            ->route('admin.modules.edit', $module)
            ->with('success', 'Level updated successfully');
    }

    public function destroy(Module $module, Level $level)
    {
        $this->ensureLevelBelongsToModule($module, $level);

        $level->delete();

        return redirect()
            ->route('admin.modules.edit', $module)
            ->with('success', 'Level deleted successfully');
    }

    private function ensureLevelBelongsToModule(Module $module, Level $level): void
    {
        if ((int) $level->module_id !== (int) $module->id) {
            abort(404);
        }
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
}
