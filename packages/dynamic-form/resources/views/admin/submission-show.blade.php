@extends('dynamic-form::layouts.app')

@section('content')
    @php
        $currentStep = $submission->currentWorkflowStep();
    @endphp

    <div class="stack">
        <div class="hero panel">
            <div class="toolbar">
                <div class="section-title">
                    <span class="eyebrow">Submission Detail</span>
                    <h1>{{ $submission->reference ?: 'Submission #'.$submission->id }}</h1>
                    <p>Review the submitted data, inspect the activity timeline, and advance or reject the workflow from one workspace.</p>
                </div>
                <div class="actions">
                    <span class="status-pill {{ $submission->status }}">{{ ucwords(str_replace('_', ' ', $submission->status)) }}</span>
                    @if ($submission->current_step_name)
                        <span class="pill pill-outline">Current stage: {{ $submission->current_step_name }}</span>
                    @endif
                    <a class="button secondary" href="{{ route('dynamic-form.admin.submissions.index', $form) }}">Back to submissions</a>
                </div>
            </div>
        </div>

        <div class="detail-grid">
            <div class="stack">
                <div class="panel surface stack">
                    <div class="toolbar">
                        <div class="section-title">
                            <h2>Submitted data</h2>
                            <p>Review the exact values that entered the workflow.</p>
                        </div>
                        <div class="page-footer-note">{{ $submission->created_at?->format('Y-m-d H:i:s') }}</div>
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

                <div class="panel surface stack">
                    <div class="section-title">
                        <h2>Activity timeline</h2>
                        <p>Every workflow decision, notification attempt, and automation call is recorded here.</p>
                    </div>

                    <div class="timeline">
                        @foreach ($submission->activities as $activity)
                            <div class="timeline-item stack-tight">
                                <div class="toolbar">
                                    <strong>{{ $activity->title }}</strong>
                                    <div class="timeline-meta">{{ $activity->created_at?->format('Y-m-d H:i:s') }}</div>
                                </div>
                                @if ($activity->description)
                                    <div class="meta-line">{{ $activity->description }}</div>
                                @endif
                                <div class="actions">
                                    <span class="badge">{{ ucwords(str_replace('_', ' ', $activity->type)) }}</span>
                                    @if ($activity->trigger)
                                        <span class="badge">{{ ucwords(str_replace('_', ' ', $activity->trigger)) }}</span>
                                    @endif
                                    @if ($activity->actor_name)
                                        <span class="badge">{{ $activity->actor_name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="stack">
                <div class="callout-card stack">
                    <div class="section-title">
                        <h2>Workflow controls</h2>
                        <p>Advance the current stage or stop the workflow with a rejection note.</p>
                    </div>

                    @if ($submission->isFinished())
                        <div class="status">This submission has reached a final status and no further manual action is required.</div>
                    @else
                        <form method="POST" action="{{ route('dynamic-form.admin.submissions.update', [$form, $submission]) }}" class="stack">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="notes">Decision notes</label>
                                <textarea id="notes" name="notes" placeholder="Add context for the next reviewer or explain the rejection.">{{ old('notes', $submission->decision_notes) }}</textarea>
                            </div>

                            <div class="actions">
                                <button class="button" type="submit" name="action" value="approve">
                                    {{ ($currentStep['type'] ?? 'approval') === 'review' ? 'Complete stage' : 'Approve stage' }}
                                </button>
                                <button class="button flat" type="submit" name="action" value="reject">Reject submission</button>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="panel surface stack surface-soft">
                    <div class="section-title">
                        <h2>Operational snapshot</h2>
                        <p>Key metadata for support, audits, and downstream troubleshooting.</p>
                    </div>

                    <div class="definition-list">
                        <div class="definition-row"><strong>Status:</strong> {{ ucwords(str_replace('_', ' ', $submission->status)) }}</div>
                        <div class="definition-row"><strong>Current step:</strong> {{ $submission->current_step_name ?: 'None' }}</div>
                        <div class="definition-row"><strong>IP address:</strong> {{ $submission->ip_address ?: 'Unknown' }}</div>
                        <div class="definition-row"><strong>User agent:</strong> {{ $submission->user_agent ?: 'Unknown' }}</div>
                        <div class="definition-row"><strong>Reviewed at:</strong> {{ $submission->reviewed_at?->format('Y-m-d H:i:s') ?: 'Not yet' }}</div>
                        <div class="definition-row"><strong>Approved at:</strong> {{ $submission->approved_at?->format('Y-m-d H:i:s') ?: 'Not yet' }}</div>
                        <div class="definition-row"><strong>Rejected at:</strong> {{ $submission->rejected_at?->format('Y-m-d H:i:s') ?: 'Not rejected' }}</div>
                        <div class="definition-row"><strong>Completed at:</strong> {{ $submission->completed_at?->format('Y-m-d H:i:s') ?: 'Not completed' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
