@extends('dynamic-form::layouts.app')

@section('content')
    <div class="stack">
        <div class="hero panel">
            <div class="toolbar">
                <div class="section-title">
                    <span class="eyebrow">Submissions</span>
                    <h1>{{ $form->name }} submissions</h1>
                    <p>{{ $submissions->count() }} recent submission{{ $submissions->count() === 1 ? '' : 's' }} captured through the workflow-enabled form flow.</p>
                </div>
                <div class="actions">
                    <a class="button secondary" href="{{ route('dynamic-form.admin.forms.edit', $form) }}">Edit form</a>
                    <a class="button ghost" href="{{ route('dynamic-form.public.show', $form->slug) }}" target="_blank">Open form</a>
                </div>
            </div>
        </div>

        @forelse ($submissions as $submission)
            <div class="panel surface stack surface-soft">
                <div class="toolbar">
                    <div class="section-title">
                        <h2>{{ $submission->reference ?: 'Submission #'.$submission->id }}</h2>
                        <p>{{ $submission->created_at?->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div class="actions">
                        <span class="status-pill {{ $submission->status }}">{{ ucwords(str_replace('_', ' ', $submission->status)) }}</span>
                        @if ($submission->current_step_name)
                            <span class="badge">{{ $submission->current_step_name }}</span>
                        @endif
                        <span class="badge">{{ $submission->ip_address ?: 'unknown IP' }}</span>
                        <a class="button tiny secondary" href="{{ route('dynamic-form.admin.submissions.show', [$form, $submission]) }}">Open</a>
                    </div>
                </div>

                <div class="grid-3">
                    @foreach ($form->fields->take(3) as $field)
                        @php
                            $value = $submission->data[$field->name] ?? null;
                        @endphp

                        <div class="field-card">
                            <strong>{{ $field->label }}</strong>
                            <div class="hint" style="margin-bottom: 0.6rem;">{{ $field->name }}</div>

                            @if (is_array($value) && isset($value['path']))
                                {{ $value['original_name'] }}
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
            <div class="panel empty-state">
                <div class="stack" style="justify-items: center;">
                    <h2>No submissions yet</h2>
                    <p class="hint">Open the public form and submit a test response to see saved entries, statuses, and workflow activity here.</p>
                    <a class="button" href="{{ route('dynamic-form.public.show', $form->slug) }}" target="_blank">Open public form</a>
                </div>
            </div>
        @endforelse
    </div>
@endsection
