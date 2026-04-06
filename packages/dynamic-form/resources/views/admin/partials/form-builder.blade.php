@php
    $fieldRows = old('fields', $fields->toArray());
@endphp

<div class="split-note">
    <div class="panel surface stack surface-soft">
        <div class="section-title">
            <span class="eyebrow">Form Setup</span>
            <h2>Core form settings</h2>
            <p>Set the form identity, public slug, success message, and activation state before adding fields.</p>
        </div>

        <div class="grid-2">
            <div>
                <label for="name">Form name</label>
                <input id="name" name="name" value="{{ old('name', $form->name) }}" required>
            </div>
            <div>
                <label for="slug">Slug</label>
                <input id="slug" name="slug" value="{{ old('slug', $form->slug) }}" required>
                <div class="hint">Used in the public URL and in the `@dynamicForm('slug')` Blade directive.</div>
            </div>
        </div>

        <div class="grid-2">
            <div>
                <label for="submit_label">Submit button label</label>
                <input id="submit_label" name="submit_label" value="{{ old('submit_label', $form->submit_label) }}">
            </div>
            <div>
                <label for="success_message">Success message</label>
                <input id="success_message" name="success_message" value="{{ old('success_message', $form->success_message) }}">
            </div>
        </div>

        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $form->description) }}</textarea>
        </div>

        <label class="inline-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $form->is_active))>
            <span>Form is active and publicly available</span>
        </label>
    </div>

    <div class="aside-note stack">
        <strong>Production checklist</strong>
        <p class="hint">Use a stable slug, set a clear success message, and keep fields ordered so the public experience feels deliberate.</p>
        <span class="badge">Blade Directive Ready</span>
        <span class="badge">Public Route Ready</span>
        <span class="badge">Submission Tracking On</span>
    </div>
</div>

<div class="panel surface stack">
    <div class="toolbar">
        <div class="section-title">
            <span class="eyebrow">Field Builder</span>
            <h2>Dynamic fields</h2>
            <p>Add, remove, and reorder field definitions without touching PHP code.</p>
        </div>
        <button class="button secondary" type="button" id="add-field">Add field</button>
    </div>

    <div class="stack" id="field-list">
        @foreach ($fieldRows as $index => $field)
            @include('dynamic-form::admin.partials.field-row', ['index' => $index, 'field' => $field, 'fieldTypes' => $fieldTypes])
        @endforeach
    </div>
</div>

<template id="field-template">
    @include('dynamic-form::admin.partials.field-row', ['index' => '__INDEX__', 'field' => [
        'label' => '',
        'name' => '',
        'type' => 'text',
        'placeholder' => '',
        'help_text' => '',
        'options' => '',
        'validation' => '',
        'is_required' => false,
        'sort_order' => '',
    ], 'fieldTypes' => $fieldTypes])
</template>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fieldList = document.getElementById('field-list');
        const template = document.getElementById('field-template').innerHTML;
        const addButton = document.getElementById('add-field');

        const renumber = () => {
            [...fieldList.querySelectorAll('[data-field-row]')].forEach((row, index) => {
                const number = row.querySelector('[data-field-number]');

                if (number) {
                    number.textContent = index + 1;
                }

                row.querySelectorAll('[data-field-input]').forEach((input) => {
                    input.name = input.name.replace(/fields\[\d+\]/, `fields[${index}]`);
                    input.id = input.id.replace(/fields_\d+_/, `fields_${index}_`);
                });
            });
        };

        addButton.addEventListener('click', () => {
            const index = fieldList.querySelectorAll('[data-field-row]').length;
            fieldList.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', index));
            renumber();
        });

        fieldList.addEventListener('click', (event) => {
            const action = event.target.closest('[data-field-action]');

            if (! action) {
                return;
            }

            const row = event.target.closest('[data-field-row]');

            if (action.dataset.fieldAction === 'remove') {
                row.remove();
                renumber();
            }

            if (action.dataset.fieldAction === 'up' && row.previousElementSibling) {
                row.parentNode.insertBefore(row, row.previousElementSibling);
                renumber();
            }

            if (action.dataset.fieldAction === 'down' && row.nextElementSibling) {
                row.parentNode.insertBefore(row.nextElementSibling, row);
                renumber();
            }
        });
    });
</script>
