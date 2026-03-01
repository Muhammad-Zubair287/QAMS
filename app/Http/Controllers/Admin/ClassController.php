<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * ClassController — QAMS Admin
 * ─────────────────────────────────────────────────────────────────────────
 * OOP: Encapsulation — all class/section CRUD operations in one place.
 * Handles: list classes, add class, edit class, delete class.
 */
class ClassController extends Controller
{
    /** GET /admin/classes */
    public function index(): View
    {
        $classes = SchoolClass::withCount('subjects')->orderBy('name')->get();
        return view('admin.classes.index', compact('classes'));
    }

    /** GET /admin/classes/create */
    public function create(): View
    {
        return view('admin.classes.create');
    }

    /** POST /admin/classes */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => 'required|string|max:50',
            'section' => 'nullable|string|max:10',
        ], [
            'name.required' => 'Class name is required.',
        ]);

        try {
            $class = SchoolClass::create([
                'name'    => $request->input('name'),
                'section' => $request->input('section'),
            ]);
            Log::info('Admin created class', ['class_id' => $class->id, 'name' => $class->name]);
        } catch (\Exception $e) {
            Log::error('Class creation failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create class. Please try again.']);
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class "' . $class->full_name . '" created successfully.');
    }

    /** GET /admin/classes/{id}/edit */
    public function edit(int $id): View|RedirectResponse
    {
        $class = SchoolClass::with('subjects')->find($id);
        if (!$class) {
            return redirect()->route('admin.classes.index')
                ->withErrors(['error' => 'Class not found.']);
        }
        return view('admin.classes.edit', compact('class'));
    }

    /** PUT /admin/classes/{id} */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name'    => 'required|string|max:50',
            'section' => 'nullable|string|max:10',
        ]);

        $class = SchoolClass::find($id);
        if (!$class) {
            return redirect()->route('admin.classes.index')
                ->withErrors(['error' => 'Class not found.']);
        }

        $class->update([
            'name'    => $request->input('name'),
            'section' => $request->input('section'),
        ]);

        Log::info('Admin updated class', ['class_id' => $id]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    /** DELETE /admin/classes/{id} */
    public function destroy(int $id): RedirectResponse
    {
        $class = SchoolClass::find($id);
        if (!$class) {
            return redirect()->route('admin.classes.index')
                ->withErrors(['error' => 'Class not found.']);
        }

        $name = $class->full_name;
        $class->delete(); // Cascade deletes subjects

        Log::info('Admin deleted class', ['class_id' => $id, 'name' => $name]);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class "' . $name . '" and all its subjects deleted.');
    }
}
