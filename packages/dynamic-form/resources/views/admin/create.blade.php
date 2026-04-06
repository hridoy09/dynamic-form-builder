@extends('dynamic-form::layouts.app')

@section('content')
    <form method="POST" action="{{ route('dynamic-form.admin.forms.store') }}" class="stack">
        @csrf

        <div class="actions" style="justify-content: space-between;">
            <div>
                <h1 style="margin: 0 0 0.4rem;">Create form</h1>
                <div class="hint">Define the form shell, add dynamic fields, and publish it when ready.</div>
            </div>
            <div class="actions">
                <a class="button secondary" href="{{ route('dynamic-form.admin.forms.index') }}">Back</a>
                <button class="button" type="submit">Save form</button>
            </div>
        </div>

        @include('dynamic-form::admin.partials.form-builder')
    </form>
@endsection
