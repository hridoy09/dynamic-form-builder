<?php

use FomBuilder\DynamicForm\Http\Controllers\Admin\FormController as AdminFormController;
use FomBuilder\DynamicForm\Http\Controllers\Admin\SubmissionController;
use FomBuilder\DynamicForm\Http\Controllers\FormController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('dynamic-form.route_middleware', ['web']))
    ->prefix(config('dynamic-form.route_prefix', 'dynamic-forms'))
    ->as('dynamic-form.admin.')
    ->group(function () {
        Route::get('/', [AdminFormController::class, 'index'])->name('forms.index');
        Route::get('/create', [AdminFormController::class, 'create'])->name('forms.create');
        Route::post('/', [AdminFormController::class, 'store'])->name('forms.store');
        Route::get('/{form}/edit', [AdminFormController::class, 'edit'])->name('forms.edit');
        Route::put('/{form}', [AdminFormController::class, 'update'])->name('forms.update');
        Route::get('/{form}/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/{form}/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
        Route::patch('/{form}/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    });

Route::middleware(config('dynamic-form.public_route_middleware', ['web']))
    ->prefix(config('dynamic-form.public_route_prefix', 'forms'))
    ->as('dynamic-form.public.')
    ->group(function () {
        Route::get('/{slug}', [FormController::class, 'show'])->name('show');
        Route::post('/{slug}', [FormController::class, 'submit'])->name('submit');
    });
