<?php

namespace FomBuilder\DynamicForm\Http\Controllers\Admin;

use FomBuilder\DynamicForm\Models\DynamicForm;
use FomBuilder\DynamicForm\Models\DynamicFormSubmission;
use FomBuilder\DynamicForm\Services\DynamicFormWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubmissionController extends Controller
{
    public function index(DynamicForm $form)
    {
        $form->load([
            'fields',
            'submissions' => fn ($query) => $query->latest()->limit(25),
        ]);

        return view('dynamic-form::admin.submissions', [
            'form' => $form,
            'submissions' => $form->submissions,
        ]);
    }

    public function show(DynamicForm $form, DynamicFormSubmission $submission)
    {
        $this->ensureSubmissionBelongsToForm($form, $submission);

        $submission->load(['form.fields', 'activities']);

        return view('dynamic-form::admin.submission-show', [
            'form' => $form,
            'submission' => $submission,
        ]);
    }

    public function update(
        Request $request,
        DynamicForm $form,
        DynamicFormSubmission $submission,
        DynamicFormWorkflowService $workflowService,
    ): RedirectResponse {
        $this->ensureSubmissionBelongsToForm($form, $submission);

        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $workflowService->progress(
            $submission,
            $validated['action'],
            $validated['notes'] ?? null,
            $request->user()?->name ?? 'Admin',
        );

        return redirect()
            ->route('dynamic-form.admin.submissions.show', [$form, $submission])
            ->with('status', 'Submission workflow updated successfully.');
    }

    protected function ensureSubmissionBelongsToForm(DynamicForm $form, DynamicFormSubmission $submission): void
    {
        abort_unless($submission->form_id === $form->id, 404);
    }
}
