@extends('dynamic-form::layouts.app')

@section('content')
    <div class="panel stack">
        <div>
            <h1 style="margin: 0 0 0.5rem;">{{ $form->name }}</h1>
            @if ($form->description)
                <p class="hint" style="margin: 0;">{{ $form->description }}</p>
            @endif
        </div>

        @include('dynamic-form::partials.form', ['form' => $form])
    </div>
@endsection
