@extends('dynamic-form::layouts.app')

@section('content')
    @php
        $totalFields = $forms->sum('fields_count');
        $totalSubmissions = $forms->sum('submissions_count');
        $totalWorkflowStages = $forms->sum('workflow_steps_count');
        $totalAutomationActions = $forms->sum('automation_actions_count');
    @endphp

    <div class="stack">
        <div class="hero panel">
            <div class="toolbar">
                <div class="section-title">
                    <span class="eyebrow">Builder Dashboard</span>
                    <h1>Manage forms with a production-ready workflow</h1>
                    <p>Create, publish, review, and automate forms from a single interface designed for real operations, not just demos.</p>
                </div>
                <div class="actions">
                    <span class="pill pill-outline">{{ $forms->count() }} form{{ $forms->count() === 1 ? '' : 's' }}</span>
                    <a class="button" href="{{ route('dynamic-form.admin.forms.create') }}">Create form</a>
                </div>
            </div>

            <div class="metric-grid">
                <div class="metric">
                    <span class="metric-label">Total Forms</span>
                    <span class="metric-value">{{ $forms->count() }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Configured Fields</span>
                    <span class="metric-value">{{ $totalFields }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Captured Submissions</span>
                    <span class="metric-value">{{ $totalSubmissions }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Workflow Stages</span>
                    <span class="metric-value">{{ $totalWorkflowStages }}</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Automation Actions</span>
                    <span class="metric-value">{{ $totalAutomationActions }}</span>
                </div>
            </div>
        </div>

        @if ($forms->isEmpty())
            <div class="panel empty-state">
                <div class="stack" style="justify-items: center;">
                    <span class="pill pill-success">Production Ready UI</span>
                    <h2>No forms yet</h2>
                    <p class="hint">Create your first form to start collecting validated submissions, approvals, and automations.</p>
                    <div class="actions">
                        <a class="button" href="{{ route('dynamic-form.admin.forms.create') }}">Create your first form</a>
                    </div>
                </div>
            </div>
        @else
            <div class="panel surface stack">
                <div class="toolbar">
                    <div class="section-title">
                        <h2>Published forms</h2>
                        <p>Every form includes field, workflow, notification, and automation counts with direct actions for editing and review.</p>
                    </div>
                    <div class="page-footer-note">Use the public URL or the Blade directive to surface each form anywhere in your app.</div>
                </div>

                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Fields</th>
                                <th>Workflow</th>
                                <th>Automation</th>
                                <th>Submissions</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($forms as $form)
                                <tr>
                                    <td>
                                        <strong>{{ $form->name }}</strong>
                                        @if ($form->description)
                                            <div class="hint">{{ $form->description }}</div>
                                        @endif
                                    </td>
                                    <td><code>{{ $form->slug }}</code></td>
                                    <td>{{ $form->fields_count }}</td>
                                    <td>{{ $form->workflow_steps_count }} stage{{ $form->workflow_steps_count === 1 ? '' : 's' }}</td>
                                    <td>{{ $form->automation_actions_count }} action{{ $form->automation_actions_count === 1 ? '' : 's' }}</td>
                                    <td>{{ $form->submissions_count }}</td>
                                    <td>
                                        <span class="status-pill {{ $form->is_active ? 'active' : 'draft' }}">{{ $form->is_active ? 'Active' : 'Draft' }}</span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a class="button tiny flat" href="{{ route('dynamic-form.public.show', $form->slug) }}" target="_blank">Open</a>
                                            <a class="button tiny secondary" href="{{ route('dynamic-form.admin.forms.edit', $form) }}">Edit</a>
                                            <a class="button tiny ghost" href="{{ route('dynamic-form.admin.submissions.index', $form) }}">Submissions</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
