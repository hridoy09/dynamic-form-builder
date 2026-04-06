@extends('dynamic-form::layouts.app')

@section('content')
    <div class="stack">
        <div class="actions" style="justify-content: space-between;">
            <div>
                <h1 style="margin: 0 0 0.4rem;">{{ $form->name }} submissions</h1>
                <div class="hint">{{ $submissions->count() }} total submission{{ $submissions->count() === 1 ? '' : 's' }}</div>
            </div>
            <div class="actions">
                <a class="button secondary" href="{{ route('dynamic-form.admin.forms.edit', $form) }}">Edit form</a>
                <a class="button secondary" href="{{ route('dynamic-form.public.show', $form->slug) }}" target="_blank">Open form</a>
            </div>
        </div>

        @forelse ($submissions as $submission)
            <div class="panel stack">
                <div class="actions" style="justify-content: space-between;">
                    <div>
                        <strong>Submission #{{ $submission->id }}</strong>
                        <div class="hint">{{ $submission->created_at?->format('Y-m-d H:i:s') }}</div>
                    </div>
                    <div class="badge">{{ $submission->ip_address ?: 'unknown IP' }}</div>
                </div>

                <div class="grid-2">
                    @foreach ($form->fields as $field)
                        @php
                            $value = $submission->data[$field->name] ?? null;
                        @endphp

                        <div class="field-card">
                            <strong>{{ $field->label }}</strong>
                            <div class="hint" style="margin-bottom: 0.6rem;">{{ $field->name }}</div>

                            @if (is_array($value) && isset($value['path']))
                                @php
                                    $fileUrl = \Illuminate\Support\Facades\Storage::disk($value['disk'])->url($value['path']);
                                @endphp
                                <a href="{{ $fileUrl }}" target="_blank">{{ $value['original_name'] }}</a>
                            @elseif (is_array($value))
                                {{ implode(', ', $value) }}
                            @elseif ($value === null || $value === '')
                                <span class="hint">No value</span>
                            @else
                                {{ $value }}
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="panel">
                <h2 style="margin-top: 0;">No submissions yet</h2>
                <p class="hint">Open the public form and submit a test response to see saved entries here.</p>
            </div>
        @endforelse
    </div>
@endsection
