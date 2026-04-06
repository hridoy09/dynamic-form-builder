@extends('dynamic-form::layouts.app')

@section('content')
    <form method="POST" action="{{ route('dynamic-form.admin.forms.update', $form) }}" class="stack">
        @csrf
        @method('PUT')

        <div class="actions" style="justify-content: space-between;">
            <div>
                <h1 style="margin: 0 0 0.4rem;">Edit {{ $form->name }}</h1>
                <div class="hint">Public URL: <a href="{{ route('dynamic-form.public.show', $form->slug) }}" target="_blank">{{ route('dynamic-form.public.show', $form->slug) }}</a></div>
            </div>
            <div class="actions">
                <a class="button secondary" href="{{ route('dynamic-form.admin.submissions.index', $form) }}">View submissions</a>
                <button class="button" type="submit">Update form</button>
            </div>
        </div>

        @include('dynamic-form::admin.partials.form-builder')
    </form>
@endsection
