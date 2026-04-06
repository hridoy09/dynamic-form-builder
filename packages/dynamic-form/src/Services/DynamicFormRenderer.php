<?php

namespace FomBuilder\DynamicForm\Services;

use FomBuilder\DynamicForm\Models\DynamicForm;
use Illuminate\Contracts\View\View;

class DynamicFormRenderer
{
    public function render(DynamicForm|string $form): string
    {
        return $this->view($form)->render();
    }

    public function view(DynamicForm|string $form): View
    {
        if (! $form instanceof DynamicForm) {
            $form = DynamicForm::query()
                ->where('slug', $form)
                ->where('is_active', true)
                ->with('fields')
                ->firstOrFail();
        } else {
            $form->loadMissing('fields');
        }

        return view('dynamic-form::partials.form', [
            'form' => $form,
        ]);
    }
}
