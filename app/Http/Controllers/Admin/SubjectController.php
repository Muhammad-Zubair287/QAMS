<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * SubjectController — QAMS Admin
 * ─────────────────────────────────────────────────────────────────────────
 * OOP: Encapsulation — all subject CRUD operations in one place.
 * Handles: list subjects, add subject, edit subject, delete subject.
 */
class SubjectController extends Controller
{
    /** GET /admin/subjects */
    public function index(): View
    {
        $subjects = Subject::with('schoolClass')
            ->withCount(['teachers', 'students'])
            ->orderBy('name')
            ->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    /** GET /admin/subjects/create */
    public function create(): View
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('admin.subjects.create', compact('classes'));
    }

    /** POST /admin/subjects */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'class_id' => 'required|exists:classes,id',
        ], [
            'name.required'     => 'Subject name is required.',
            'class_id.required' => 'Please select a class.',
            'class_id.exists'   => 'Selected class does not exist.',
        ]);

        try {
            $subject = Subject::create([
                'name'     => $request->input('name'),
                'class_id' => $request->input('class_id'),
            ]);
            Log::info('Admin created subject', ['subject_id' => $subject->id]);
        } catch (\Exception $e) {
            Log::error('Subject creation failed', ['error' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Failed to create subject.']);
        }

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject "' . $subject->name . '" created successfully.');
    }

    /** GET /admin/subjects/{id}/edit */
    public function edit(int $id): View|RedirectResponse
    {
        $subject = Subject::find($id);
        if (!$subject) {
            return redirect()->route('admin.subjects.index')
                ->withErrors(['error' => 'Subject not found.']);
        }
        $classes = SchoolClass::orderBy('name')->get();
        return view('admin.subjects.edit', compact('subject', 'classes'));
    }

    /** PUT /admin/subjects/{id} */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'class_id' => 'required|exists:classes,id',
        ]);

        $subject = Subject::find($id);
        if (!$subject) {
            return redirect()->route('admin.subjects.index')
                ->withErrors(['error' => 'Subject not found.']);
        }

        $subject->update([
            'name'     => $request->input('name'),
            'class_id' => $request->input('class_id'),
        ]);

        Log::info('Admin updated subject', ['subject_id' => $id]);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    /** DELETE /admin/subjects/{id} */
    public function destroy(int $id): RedirectResponse
    {
        $subject = Subject::find($id);
        if (!$subject) {
            return redirect()->route('admin.subjects.index')
                ->withErrors(['error' => 'Subject not found.']);
        }

        $name = $subject->name;
        $subject->delete();

        Log::info('Admin deleted subject', ['subject_id' => $id, 'name' => $name]);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject "' . $name . '" deleted.');
    }
}
