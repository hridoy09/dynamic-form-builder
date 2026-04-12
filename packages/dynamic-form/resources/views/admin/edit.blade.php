@extends('dynamic-form::layouts.app')

@section('content')
    <form method="POST" action="{{ route('dynamic-form.admin.forms.update', $form) }}" class="stack">
        @csrf
        @method('PUT')

        <div class="hero panel">
            <div class="toolbar">
                <div class="section-title">
                    <span class="eyebrow">Edit Form</span>
                    <h1>{{ $form->name }}</h1>
                    <p>Refine the structure, keep the public URL stable, and evolve the workflow without rebuilding the form from scratch.</p>
                </div>
                <div class="actions">
                    <span class="pill {{ $form->is_active ? 'pill-success' : 'pill-outline' }}">{{ $form->is_active ? 'Live workflow' : 'Draft workflow' }}</span>
                    <a class="button secondary" href="{{ route('dynamic-form.admin.submissions.index', $form) }}">View submissions</a>
                    <button class="button" type="submit">Update form</button>
                </div>
            </div>
            <div class="aside-note stack">
                <strong>Public URL</strong>
                <div class="hint" style="margin-top: 0.45rem;">
                    <a href="{{ route('dynamic-form.public.show', $form->slug) }}" target="_blank">{{ route('dynamic-form.public.show', $form->slug) }}</a>
                </div>
                <span class="badge">{{ count($form->workflowSteps()) }} workflow stage{{ count($form->workflowSteps()) === 1 ? '' : 's' }}</span>
                <span class="badge">{{ count($form->automationActions()) }} automation action{{ count($form->automationActions()) === 1 ? '' : 's' }}</span>
            </div>
        </div>

        @include('dynamic-form::admin.partials.form-builder')
    </form>
@endsection
