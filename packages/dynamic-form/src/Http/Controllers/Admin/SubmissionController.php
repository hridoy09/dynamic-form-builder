<?php

namespace FomBuilder\DynamicForm\Http\Controllers\Admin;

use FomBuilder\DynamicForm\Models\DynamicForm;
use Illuminate\Routing\Controller;

class SubmissionController extends Controller
{
    public function index(DynamicForm $form)
    {
        $form->load(['fields', 'submissions']);

        return view('dynamic-form::admin.submissions', [
            'form' => $form,
            'submissions' => $form->submissions,
        ]);
    }
}
