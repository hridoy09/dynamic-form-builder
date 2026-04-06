@extends('dynamic-form::layouts.app')

@section('content')
    <div class="panel stack">
        <div class="actions" style="justify-content: space-between;">
            <div>
                <h1 style="margin: 0 0 0.4rem;">Forms</h1>
                <div class="hint">Build forms with dynamic fields, validation rules, and file upload support.</div>
            </div>
            <a class="button" href="{{ route('dynamic-form.admin.forms.create') }}">Create form</a>
        </div>

        @if ($forms->isEmpty())
            <div class="panel">
                <h2 style="margin-top: 0;">No forms yet</h2>
                <p class="hint">Create your first form to start collecting submissions.</p>
            </div>
        @else
            <div class="panel" style="padding: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Fields</th>
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
                                <td>{{ $form->submissions_count }}</td>
                                <td>{{ $form->is_active ? 'Active' : 'Draft' }}</td>
                                <td class="actions">
                                    <a href="{{ route('dynamic-form.public.show', $form->slug) }}" target="_blank">Open</a>
                                    <a href="{{ route('dynamic-form.admin.forms.edit', $form) }}">Edit</a>
                                    <a href="{{ route('dynamic-form.admin.submissions.index', $form) }}">Submissions</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
