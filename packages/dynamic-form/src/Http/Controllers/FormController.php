<?php

namespace FomBuilder\DynamicForm\Http\Controllers;

use FomBuilder\DynamicForm\Models\DynamicForm;
use FomBuilder\DynamicForm\Services\DynamicFormSubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FormController extends Controller
{
    public function show(string $slug)
    {
        $form = DynamicForm::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with('fields')
            ->firstOrFail();

        return view('dynamic-form::show', compact('form'));
    }

    public function submit(Request $request, string $slug, DynamicFormSubmissionService $submissionService): RedirectResponse
    {
        $form = DynamicForm::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with('fields')
            ->firstOrFail();

        $submissionService->submit($request, $form);

        return redirect()
            ->route('dynamic-form.public.show', $form->slug)
            ->with('status', $form->success_message ?: 'Thanks, your form has been submitted.');
    }
}
