@php
    $fieldRows = old('fields', $fields->toArray());
    $workflowRows = old('workflow_steps', $workflowSteps->toArray());
    $notificationRows = old('notification_rules', $notificationRules->toArray());
    $automationRows = old('automation_actions', $automationActions->toArray());
    $publicUrl = $form->slug ? route('dynamic-form.public.show', $form->slug) : url(trim(config('dynamic-form.public_route_prefix', 'forms'), '/').'/your-form-slug');
@endphp

<div class="panel surface stack">
    <div class="toolbar">
        <div class="section-title">
            <span class="eyebrow">Builder Studio</span>
            <h2>Shape the full submission journey</h2>
            <p>Design the form itself, define who reviews entries, decide who gets notified, and connect downstream systems from one screen.</p>
        </div>
        <div class="page-footer-note">Use the tokens in notifications and payloads to make every automation feel product-grade.</div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <span class="summary-label">Fields</span>
            <span class="summary-value">{{ count($fieldRows) }}</span>
            <div class="hint">Collected inputs</div>
        </div>
        <div class="summary-card">
            <span class="summary-label">Workflow Stages</span>
            <span class="summary-value">{{ count($workflowRows) }}</span>
            <div class="hint">Review and approval steps</div>
        </div>
        <div class="summary-card">
            <span class="summary-label">Notifications</span>
            <span class="summary-value">{{ count($notificationRows) }}</span>
            <div class="hint">Triggered email rules</div>
        </div>
        <div class="summary-card">
            <span class="summary-label">Automations</span>
            <span class="summary-value">{{ count($automationRows) }}</span>
            <div class="hint">Webhook and API actions</div>
        </div>
    </div>
</div>

<div class="split-note">
    <div class="panel surface stack surface-soft">
        <div class="section-title">
            <span class="eyebrow">Form Setup</span>
            <h2>Core form settings</h2>
            <p>Set the form identity, public slug, success message, and activation state before layering on the workflow.</p>
        </div>

        <div class="grid-2">
            <div>
                <label for="name">Form name</label>
                <input id="name" name="name" value="{{ old('name', $form->name) }}" required>
            </div>
            <div>
                <label for="slug">Slug</label>
                <input id="slug" name="slug" value="{{ old('slug', $form->slug) }}" required>
                <div class="hint">Used in the public URL and the <code>@dynamicForm('slug')</code> Blade directive.</div>
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
        <strong>Experience blueprint</strong>
        <div class="stack-tight">
            <span class="badge">Builder-first UI</span>
            <span class="badge">Approval-ready workflow</span>
            <span class="badge">Webhook automation</span>
        </div>
        <div class="route-preview">
            <strong>Public URL</strong>
            <div class="hint" style="margin-top: 0.35rem;">{{ $publicUrl }}</div>
        </div>
        <div class="route-preview">
            <strong>Suggested token set</strong>
            <div class="hint" style="margin-top: 0.35rem;">
                @verbatim
                    {{ form.name }}, {{ submission.reference }}, {{ submission.status }}, {{ current_step.name }}
                @endverbatim
            </div>
        </div>
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

<div class="panel surface stack">
    <div class="toolbar">
        <div class="section-title">
            <span class="eyebrow">Workflow Builder</span>
            <h2>Approval flow</h2>
            <p>Create manual review and approval stages so submissions move through a real operational process.</p>
        </div>
        <button class="button secondary" type="button" id="add-workflow-step">Add stage</button>
    </div>

    <div class="stack" id="workflow-step-list">
        @foreach ($workflowRows as $index => $step)
            @include('dynamic-form::admin.partials.workflow-step-row', ['index' => $index, 'step' => $step, 'workflowStepTypes' => $workflowStepTypes])
        @endforeach
    </div>
</div>

<div class="panel surface stack">
    <div class="toolbar">
        <div class="section-title">
            <span class="eyebrow">Notifications</span>
            <h2>Keep teams informed</h2>
            <p>Trigger email updates when forms are submitted, approvals are waiting, or outcomes change.</p>
        </div>
        <button class="button secondary" type="button" id="add-notification-rule">Add notification</button>
    </div>

    <div class="stack" id="notification-rule-list">
        @foreach ($notificationRows as $index => $rule)
            @include('dynamic-form::admin.partials.notification-rule-row', ['index' => $index, 'rule' => $rule, 'workflowTriggers' => $workflowTriggers])
        @endforeach
    </div>
</div>

<div class="panel surface stack">
    <div class="toolbar">
        <div class="section-title">
            <span class="eyebrow">Automations</span>
            <h2>API and webhook actions</h2>
            <p>Send structured payloads to downstream systems as soon as a submission is approved, rejected, or completed.</p>
        </div>
        <button class="button secondary" type="button" id="add-automation-action">Add automation</button>
    </div>

    <div class="stack" id="automation-action-list">
        @foreach ($automationRows as $index => $action)
            @include('dynamic-form::admin.partials.automation-action-row', [
                'index' => $index,
                'action' => $action,
                'workflowTriggers' => $workflowTriggers,
                'automationTypes' => $automationTypes,
                'automationMethods' => $automationMethods,
            ])
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

<template id="workflow-step-template">
    @include('dynamic-form::admin.partials.workflow-step-row', ['index' => '__INDEX__', 'step' => [
        'name' => '',
        'type' => 'approval',
        'assignee' => '',
        'instructions' => '',
        'sla_hours' => '',
    ], 'workflowStepTypes' => $workflowStepTypes])
</template>

<template id="notification-rule-template">
    @include('dynamic-form::admin.partials.notification-rule-row', ['index' => '__INDEX__', 'rule' => [
        'label' => '',
        'trigger' => 'submitted',
        'channel' => 'email',
        'recipient' => '',
        'subject' => '',
        'message' => '',
        'is_active' => true,
    ], 'workflowTriggers' => $workflowTriggers])
</template>

<template id="automation-action-template">
    @include('dynamic-form::admin.partials.automation-action-row', ['index' => '__INDEX__', 'action' => [
        'label' => '',
        'trigger' => 'completed',
        'type' => 'webhook',
        'endpoint' => '',
        'method' => 'POST',
        'headers' => '',
        'payload' => '',
        'is_active' => true,
    ], 'workflowTriggers' => $workflowTriggers, 'automationTypes' => $automationTypes, 'automationMethods' => $automationMethods])
</template>

<div class="modal-backdrop" id="field-option-modal" hidden>
    <div class="modal-shell panel stack" role="dialog" aria-modal="true" aria-labelledby="field-option-modal-title">
        <div class="toolbar">
            <div class="section-title">
                <span class="eyebrow">Option Builder</span>
                <h2 id="field-option-modal-title">Manage field options</h2>
                <p>Add the display label and the stored value for each option in this field.</p>
            </div>
            <button type="button" class="button ghost" id="close-field-option-modal">Close</button>
        </div>

        <div class="option-editor-list" id="field-option-modal-list"></div>

        <div class="actions">
            <button type="button" class="button secondary" id="add-modal-option-row">Add option</button>
            <button type="button" class="button ghost" id="cancel-field-option-modal">Cancel</button>
            <button type="button" class="button" id="save-field-option-modal">Save options</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const choiceFieldTypes = new Set(['select', 'radio', 'checkbox']);
        const fieldList = document.getElementById('field-list');
        const optionModal = document.getElementById('field-option-modal');
        const optionModalList = document.getElementById('field-option-modal-list');
        const optionModalTitle = document.getElementById('field-option-modal-title');
        const addModalOptionRowButton = document.getElementById('add-modal-option-row');
        const saveOptionModalButton = document.getElementById('save-field-option-modal');
        const closeOptionModalButtons = [
            document.getElementById('close-field-option-modal'),
            document.getElementById('cancel-field-option-modal'),
        ];
        let activeFieldRow = null;

        const escapeHtml = (value) => String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const slugify = (value) => String(value)
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');

        const normalizeOption = (option) => {
            if (typeof option === 'string') {
                const trimmed = option.trim();

                if (! trimmed) {
                    return null;
                }

                return {
                    label: trimmed,
                    value: trimmed,
                };
            }

            if (! option || typeof option !== 'object') {
                return null;
            }

            const label = String(option.label ?? option.name ?? option.value ?? '').trim();
            const value = String(option.value ?? '').trim();

            if (! label && ! value) {
                return null;
            }

            const fallback = slugify(label) || label || value;

            return {
                label: label || value,
                value: value || fallback,
            };
        };

        const parseStoredOptions = (rawValue) => {
            const trimmed = String(rawValue ?? '').trim();

            if (! trimmed) {
                return [];
            }

            try {
                const parsed = JSON.parse(trimmed);

                if (Array.isArray(parsed)) {
                    return parsed.map(normalizeOption).filter(Boolean);
                }
            } catch (error) {
            }

            return trimmed
                .split(/\r\n|\r|\n/)
                .map(normalizeOption)
                .filter(Boolean);
        };

        const serializeStoredOptions = (options) => {
            return options.length ? JSON.stringify(options) : '';
        };

        const optionSummary = (count) => {
            if (count === 0) {
                return 'No options yet';
            }

            return `${count} option${count === 1 ? '' : 's'} configured`;
        };

        const renderFieldOptionPreview = (row) => {
            if (! row) {
                return;
            }

            const typeInput = row.querySelector('[data-field-type]');
            const panel = row.querySelector('[data-option-panel]');
            const disabledHint = row.querySelector('[data-option-disabled]');
            const preview = row.querySelector('[data-option-preview]');
            const summary = row.querySelector('[data-option-summary]');
            const store = row.querySelector('[data-option-store]');
            const openButton = row.querySelector('[data-option-open]');
            const isChoiceField = choiceFieldTypes.has(typeInput?.value ?? '');

            if (panel) {
                panel.hidden = ! isChoiceField;
            }

            if (disabledHint) {
                disabledHint.hidden = isChoiceField;
            }

            if (! isChoiceField || ! preview || ! summary || ! store) {
                return;
            }

            const options = parseStoredOptions(store.value);
            summary.textContent = optionSummary(options.length);

            if (openButton) {
                openButton.textContent = options.length ? 'Manage options' : 'Add option';
            }

            if (! options.length) {
                preview.innerHTML = '<span class="hint">Use the option builder to add labels and stored values for this field.</span>';

                return;
            }

            preview.innerHTML = options.map((option) => {
                return `<span class="option-pill"><strong>${escapeHtml(option.label)}</strong><span>${escapeHtml(option.value)}</span></span>`;
            }).join('');
        };

        const syncFieldOptionPanels = () => {
            if (! fieldList) {
                return;
            }

            [...fieldList.querySelectorAll('[data-repeater-row]')].forEach(renderFieldOptionPreview);
        };

        const createOptionEditorRow = (option = { label: '', value: '' }) => {
            const item = document.createElement('div');
            item.className = 'option-editor-row stack-tight';
            item.innerHTML = `
                <div class="grid-2">
                    <div>
                        <label>Option name</label>
                        <input type="text" data-option-label value="${escapeHtml(option.label ?? '')}" placeholder="Premium plan">
                    </div>
                    <div>
                        <label>Option value</label>
                        <input type="text" data-option-value value="${escapeHtml(option.value ?? '')}" placeholder="premium-plan">
                    </div>
                </div>
                <div class="actions">
                    <button type="button" class="button tiny flat" data-option-remove>Remove option</button>
                </div>
            `;

            return item;
        };

        const openOptionModal = (row) => {
            activeFieldRow = row;

            if (! optionModal || ! optionModalList) {
                return;
            }

            const store = row.querySelector('[data-option-store]');
            const labelInput = row.querySelector('[name$="[label]"]');
            const options = parseStoredOptions(store?.value ?? '');

            optionModalTitle.textContent = labelInput?.value
                ? `Manage options for ${labelInput.value}`
                : 'Manage field options';

            optionModalList.innerHTML = '';

            if (! options.length) {
                optionModalList.appendChild(createOptionEditorRow());
            } else {
                options.forEach((option) => optionModalList.appendChild(createOptionEditorRow(option)));
            }

            optionModal.hidden = false;
            document.body.style.overflow = 'hidden';
        };

        const closeOptionModal = () => {
            activeFieldRow = null;

            if (optionModal) {
                optionModal.hidden = true;
            }

            document.body.style.overflow = '';
        };

        const collectModalOptions = () => {
            if (! optionModalList) {
                return [];
            }

            return [...optionModalList.querySelectorAll('.option-editor-row')]
                .map((row) => {
                    const labelInput = row.querySelector('[data-option-label]');
                    const valueInput = row.querySelector('[data-option-value]');

                    return normalizeOption({
                        label: labelInput?.value ?? '',
                        value: valueInput?.value ?? '',
                    });
                })
                .filter(Boolean);
        };

        const setupRepeater = ({ listId, templateId, addButtonId, prefix, idPrefix }) => {
            const list = document.getElementById(listId);
            const template = document.getElementById(templateId);
            const addButton = document.getElementById(addButtonId);

            if (! list || ! template || ! addButton) {
                return;
            }

            const renumber = () => {
                [...list.querySelectorAll('[data-repeater-row]')].forEach((row, index) => {
                    const number = row.querySelector('[data-repeater-number]');

                    if (number) {
                        number.textContent = index + 1;
                    }

                    row.querySelectorAll('[data-repeater-input]').forEach((input) => {
                        input.name = input.name.replace(new RegExp(`${prefix}\\[\\d+\\]`), `${prefix}[${index}]`);
                        input.id = input.id.replace(new RegExp(`${idPrefix}_\\d+_`), `${idPrefix}_${index}_`);
                    });
                });

                if (listId === 'field-list') {
                    syncFieldOptionPanels();
                }
            };

            addButton.addEventListener('click', () => {
                const index = list.querySelectorAll('[data-repeater-row]').length;
                list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', index));
                renumber();
            });

            list.addEventListener('click', (event) => {
                const action = event.target.closest('[data-repeater-action]');

                if (! action) {
                    return;
                }

                const row = event.target.closest('[data-repeater-row]');

                if (! row) {
                    return;
                }

                if (action.dataset.repeaterAction === 'remove') {
                    row.remove();
                    renumber();
                }

                if (action.dataset.repeaterAction === 'up' && row.previousElementSibling) {
                    row.parentNode.insertBefore(row, row.previousElementSibling);
                    renumber();
                }

                if (action.dataset.repeaterAction === 'down' && row.nextElementSibling) {
                    row.parentNode.insertBefore(row.nextElementSibling, row);
                    renumber();
                }
            });

            renumber();
        };

        [
            { listId: 'field-list', templateId: 'field-template', addButtonId: 'add-field', prefix: 'fields', idPrefix: 'fields' },
            { listId: 'workflow-step-list', templateId: 'workflow-step-template', addButtonId: 'add-workflow-step', prefix: 'workflow_steps', idPrefix: 'workflow_steps' },
            { listId: 'notification-rule-list', templateId: 'notification-rule-template', addButtonId: 'add-notification-rule', prefix: 'notification_rules', idPrefix: 'notification_rules' },
            { listId: 'automation-action-list', templateId: 'automation-action-template', addButtonId: 'add-automation-action', prefix: 'automation_actions', idPrefix: 'automation_actions' },
        ].forEach(setupRepeater);

        if (fieldList) {
            syncFieldOptionPanels();

            fieldList.addEventListener('change', (event) => {
                const typeInput = event.target.closest('[data-field-type]');

                if (! typeInput) {
                    return;
                }

                renderFieldOptionPreview(typeInput.closest('[data-repeater-row]'));
            });

            fieldList.addEventListener('click', (event) => {
                const openButton = event.target.closest('[data-option-open]');

                if (! openButton) {
                    return;
                }

                const row = openButton.closest('[data-repeater-row]');

                if (row) {
                    openOptionModal(row);
                }
            });
        }

        if (addModalOptionRowButton) {
            addModalOptionRowButton.addEventListener('click', () => {
                optionModalList?.appendChild(createOptionEditorRow());
            });
        }

        if (optionModalList) {
            optionModalList.addEventListener('click', (event) => {
                const removeButton = event.target.closest('[data-option-remove]');

                if (! removeButton) {
                    return;
                }

                removeButton.closest('.option-editor-row')?.remove();

                if (! optionModalList.children.length) {
                    optionModalList.appendChild(createOptionEditorRow());
                }
            });

            optionModalList.addEventListener('input', (event) => {
                const labelInput = event.target.closest('[data-option-label]');

                if (! labelInput) {
                    return;
                }

                const row = labelInput.closest('.option-editor-row');
                const valueInput = row?.querySelector('[data-option-value]');

                if (valueInput && valueInput.value.trim() === '') {
                    valueInput.value = slugify(labelInput.value);
                }
            });
        }

        closeOptionModalButtons.forEach((button) => {
            button?.addEventListener('click', (event) => {
                event.preventDefault();
                closeOptionModal();
            });
        });

        optionModal?.addEventListener('click', (event) => {
            if (event.target === optionModal) {
                closeOptionModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && optionModal && ! optionModal.hidden) {
                closeOptionModal();
            }
        });

        saveOptionModalButton?.addEventListener('click', () => {
            if (! activeFieldRow) {
                closeOptionModal();

                return;
            }

            const store = activeFieldRow.querySelector('[data-option-store]');

            if (store) {
                store.value = serializeStoredOptions(collectModalOptions());
            }

            renderFieldOptionPreview(activeFieldRow);
            closeOptionModal();
        });
    });
</script>
