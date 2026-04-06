@extends('dynamic-form::layouts.app')

@section('content')
    <div class="stack">
        <div class="hero panel">
            <div class="toolbar">
                <div class="section-title">
                    <span class="eyebrow">Public Form</span>
                    <h1>{{ $form->name }}</h1>
                    <p>{{ $form->description ?: 'This form is styled for production use and ready to collect validated submissions.' }}</p>
                </div>
                <div class="actions">
                    <span class="pill pill-success">Production Ready</span>
                    <span class="pill pill-outline">Slug: {{ $form->slug }}</span>
                </div>
            </div>
        </div>

        <div class="split-note">
            <div class="panel surface stack surface-soft">
                @include('dynamic-form::partials.form', ['form' => $form])
            </div>
            <div class="aside-note stack">
                <strong>Submission notes</strong>
                <p class="hint">Required fields are marked clearly, files are uploaded to your configured disk, and successful submissions are stored for review in the admin panel.</p>
                <span class="badge">Secure Blade Rendering</span>
                <span class="badge">Validation Enabled</span>
                <span class="badge">File Upload Ready</span>
            </div>
        </div>
    </div>
@endsection
