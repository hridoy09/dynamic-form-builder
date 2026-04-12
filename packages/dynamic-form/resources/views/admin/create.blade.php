@extends('dynamic-form::layouts.app')

@section('content')
    <form method="POST" action="{{ route('dynamic-form.admin.forms.store') }}" class="stack">
        @csrf

        <div class="hero panel">
            <div class="toolbar">
                <div class="section-title">
                    <span class="eyebrow">New Form</span>
                    <h1>Create a professional intake workflow</h1>
                    <p>Define the form shell, configure dynamic fields, map approvals, and publish automations for public submissions.</p>
                </div>
                <div class="actions">
                    <span class="pill pill-success">Production Ready</span>
                    <a class="button secondary" href="{{ route('dynamic-form.admin.forms.index') }}">Back</a>
                    <button class="button" type="submit">Save form</button>
                </div>
            </div>
        </div>

        @include('dynamic-form::admin.partials.form-builder')
    </form>
@endsection
