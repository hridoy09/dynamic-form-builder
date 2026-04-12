<?php

namespace FomBuilder\DynamicForm\Http\Controllers\Admin;

use FomBuilder\DynamicForm\Models\DynamicForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FormController extends Controller
{
    public function index()
    {
        $forms = DynamicForm::query()
            ->withCount(['fields', 'submissions'])
            ->latest()
            ->get()
            ->each(function (DynamicForm $form): void {
                $form->setAttribute('workflow_steps_count', count($form->workflowSteps()));
                $form->setAttribute('notification_rules_count', count($form->notificationRules()));
                $form->setAttribute('automation_actions_count', count($form->automationActions()));
            });

        return view('dynamic-form::admin.index', compact('forms'));
    }

    public function create()
    {
        return view('dynamic-form::admin.create', [
            'form' => new DynamicForm([
                'submit_label' => 'Submit',
                'success_message' => 'Thanks, your form has been submitted.',
                'is_active' => true,
            ]),
            'fieldTypes' => $this->fieldTypes(),
            'workflowStepTypes' => $this->workflowStepTypes(),
            'workflowTriggers' => $this->workflowTriggers(),
            'automationTypes' => $this->automationTypes(),
            'automationMethods' => $this->automationMethods(),
            'fields' => collect([
                [
                    'label' => 'Name',
                    'name' => 'name',
                    'type' => 'text',
                    'placeholder' => 'Enter your name',
                    'help_text' => '',
                    'options' => '',
                    'validation' => 'max:255',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
            ]),
            'workflowSteps' => collect([
                [
                    'name' => 'Team review',
                    'type' => 'review',
                    'assignee' => 'Operations',
                    'instructions' => 'Check completeness, enrichment, and any required attachments.',
                    'sla_hours' => 24,
                ],
                [
                    'name' => 'Manager approval',
                    'type' => 'approval',
                    'assignee' => 'Team lead',
                    'instructions' => 'Approve, reject, or request follow-up based on the submitted data.',
                    'sla_hours' => 48,
                ],
            ]),
            'notificationRules' => collect([
                [
                    'label' => 'Ops inbox alert',
                    'trigger' => 'submitted',
                    'channel' => 'email',
                    'recipient' => '',
                    'subject' => 'New submission for {{ form.name }}',
                    'message' => "Submission {{ submission.reference }} is ready for review.\nCurrent status: {{ submission.status }}",
                    'is_active' => true,
                ],
            ]),
            'automationActions' => collect([
                [
                    'label' => 'Approved webhook',
                    'trigger' => 'approved',
                    'type' => 'webhook',
                    'endpoint' => '',
                    'method' => 'POST',
                    'headers' => '',
                    'payload' => "{\n  \"form\": \"{{ form.slug }}\",\n  \"submission\": \"{{ submission.reference }}\",\n  \"status\": \"{{ submission.status }}\"\n}",
                    'is_active' => true,
                ],
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);

        $form = DB::transaction(function () use ($validated) {
            $form = DynamicForm::query()->create($validated['form']);
            $form->fields()->createMany($validated['fields']);

            return $form;
        });

        return redirect()
            ->route('dynamic-form.admin.forms.edit', $form)
            ->with('status', 'Form created successfully.');
    }

    public function edit(DynamicForm $form)
    {
        $form->load('fields');

        return view('dynamic-form::admin.edit', [
            'form' => $form,
            'fieldTypes' => $this->fieldTypes(),
            'workflowStepTypes' => $this->workflowStepTypes(),
            'workflowTriggers' => $this->workflowTriggers(),
            'automationTypes' => $this->automationTypes(),
            'automationMethods' => $this->automationMethods(),
            'fields' => $form->fields->map(fn ($field) => [
                'label' => $field->label,
                'name' => $field->name,
                'type' => $field->type,
                'placeholder' => $field->placeholder,
                'help_text' => $field->help_text,
                'options' => json_encode($field->normalizedOptions(), JSON_UNESCAPED_UNICODE),
                'validation' => $field->validation,
                'is_required' => $field->is_required,
                'sort_order' => $field->sort_order,
            ]),
            'workflowSteps' => collect($form->workflowSteps())->map(fn (array $step) => [
                'name' => $step['name'] ?? '',
                'type' => $step['type'] ?? 'approval',
                'assignee' => $step['assignee'] ?? '',
                'instructions' => $step['instructions'] ?? '',
                'sla_hours' => $step['sla_hours'] ?? '',
            ]),
            'notificationRules' => collect($form->notificationRules())->map(fn (array $rule) => [
                'label' => $rule['label'] ?? '',
                'trigger' => $rule['trigger'] ?? 'submitted',
                'channel' => $rule['channel'] ?? 'email',
                'recipient' => $rule['recipient'] ?? '',
                'subject' => $rule['subject'] ?? '',
                'message' => $rule['message_template'] ?? '',
                'is_active' => $rule['is_active'] ?? true,
            ]),
            'automationActions' => collect($form->automationActions())->map(fn (array $action) => [
                'label' => $action['label'] ?? '',
                'trigger' => $action['trigger'] ?? 'completed',
                'type' => $action['type'] ?? 'webhook',
                'endpoint' => $action['endpoint'] ?? '',
                'method' => $action['method'] ?? 'POST',
                'headers' => $this->headersToLines($action['headers'] ?? []),
                'payload' => $action['payload_template'] ?? '',
                'is_active' => $action['is_active'] ?? true,
            ]),
        ]);
    }

    public function update(Request $request, DynamicForm $form): RedirectResponse
    {
        $validated = $this->validateRequest($request, $form);

        DB::transaction(function () use ($validated, $form) {
            $form->update($validated['form']);
            $form->fields()->delete();
            $form->fields()->createMany($validated['fields']);
        });

        return redirect()
            ->route('dynamic-form.admin.forms.edit', $form)
            ->with('status', 'Form updated successfully.');
    }

    protected function validateRequest(Request $request, ?DynamicForm $form = null): array
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('dynamic_forms', 'slug')->ignore($form?->id),
            ],
            'description' => ['nullable', 'string'],
            'submit_label' => ['nullable', 'string', 'max:255'],
            'success_message' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/'],
            'fields.*.type' => ['required', 'string', Rule::in(array_keys($this->fieldTypes()))],
            'fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'fields.*.help_text' => ['nullable', 'string', 'max:255'],
            'fields.*.options' => ['nullable', 'string'],
            'fields.*.validation' => ['nullable', 'string', 'max:255'],
            'fields.*.is_required' => ['nullable', 'boolean'],
            'fields.*.sort_order' => ['nullable', 'integer', 'min:1'],
            'workflow_steps' => ['nullable', 'array'],
            'workflow_steps.*.name' => ['nullable', 'string', 'max:255'],
            'workflow_steps.*.type' => ['nullable', 'string', Rule::in(array_keys($this->workflowStepTypes()))],
            'workflow_steps.*.assignee' => ['nullable', 'string', 'max:255'],
            'workflow_steps.*.instructions' => ['nullable', 'string', 'max:1000'],
            'workflow_steps.*.sla_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
            'notification_rules' => ['nullable', 'array'],
            'notification_rules.*.label' => ['nullable', 'string', 'max:255'],
            'notification_rules.*.trigger' => ['nullable', 'string', Rule::in($this->workflowTriggers())],
            'notification_rules.*.channel' => ['nullable', 'string', Rule::in(['email'])],
            'notification_rules.*.recipient' => ['nullable', 'string', 'max:500'],
            'notification_rules.*.subject' => ['nullable', 'string', 'max:255'],
            'notification_rules.*.message' => ['nullable', 'string', 'max:4000'],
            'notification_rules.*.is_active' => ['nullable', 'boolean'],
            'automation_actions' => ['nullable', 'array'],
            'automation_actions.*.label' => ['nullable', 'string', 'max:255'],
            'automation_actions.*.trigger' => ['nullable', 'string', Rule::in($this->workflowTriggers())],
            'automation_actions.*.type' => ['nullable', 'string', Rule::in(array_keys($this->automationTypes()))],
            'automation_actions.*.endpoint' => ['nullable', 'string', 'max:1000'],
            'automation_actions.*.method' => ['nullable', 'string', Rule::in($this->automationMethods())],
            'automation_actions.*.headers' => ['nullable', 'string', 'max:4000'],
            'automation_actions.*.payload' => ['nullable', 'string', 'max:8000'],
            'automation_actions.*.is_active' => ['nullable', 'boolean'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $names = collect($request->input('fields', []))
                ->pluck('name')
                ->filter()
                ->map(fn ($name) => strtolower($name));

            if ($names->duplicates()->isNotEmpty()) {
                $validator->errors()->add('fields', 'Each field name must be unique inside the form.');
            }
        });

        $validated = $validator->validate();

        return [
            'form' => [
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'submit_label' => $validated['submit_label'] ?? 'Submit',
                'success_message' => $validated['success_message'] ?? 'Thanks, your form has been submitted.',
                'workflow_definition' => $this->normalizeWorkflowSteps(collect($validated['workflow_steps'] ?? [])),
                'notification_settings' => $this->normalizeNotificationRules(collect($validated['notification_rules'] ?? [])),
                'automation_settings' => $this->normalizeAutomationActions(collect($validated['automation_actions'] ?? [])),
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ],
            'fields' => $this->normalizeFields(collect($validated['fields'])),
        ];
    }

    protected function normalizeFields(Collection $fields): array
    {
        return $fields
            ->values()
            ->map(function (array $field, int $index) {
                return [
                    'label' => $field['label'],
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'placeholder' => $field['placeholder'] ?? null,
                    'help_text' => $field['help_text'] ?? null,
                    'options' => $this->normalizeFieldOptions((string) ($field['options'] ?? '')),
                    'validation' => $this->normalizeValidationRules($field['validation'] ?? null),
                    'is_required' => (bool) ($field['is_required'] ?? false),
                    'sort_order' => $field['sort_order'] ?? ($index + 1),
                ];
            })
            ->all();
    }

    protected function normalizeFieldOptions(string $options): array
    {
        $trimmed = trim($options);

        if ($trimmed === '') {
            return [];
        }

        $decoded = json_decode($trimmed, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return collect($decoded)
                ->map(function ($option) {
                    if (is_array($option)) {
                        $label = trim((string) ($option['label'] ?? $option['name'] ?? $option['value'] ?? ''));
                        $value = trim((string) ($option['value'] ?? $option['label'] ?? $option['name'] ?? ''));

                        if ($label === '' && $value === '') {
                            return null;
                        }

                        return [
                            'label' => $label !== '' ? $label : $value,
                            'value' => $value !== '' ? $value : $label,
                        ];
                    }

                    $value = trim((string) $option);

                    if ($value === '') {
                        return null;
                    }

                    return [
                        'label' => $value,
                        'value' => $value,
                    ];
                })
                ->filter()
                ->values()
                ->all();
        }

        return collect(preg_split('/\r\n|\r|\n/', $trimmed))
            ->map(fn ($option) => trim($option))
            ->filter()
            ->map(fn ($option) => ['label' => $option, 'value' => $option])
            ->values()
            ->all();
    }

    protected function normalizeValidationRules(?string $rules): ?string
    {
        if ($rules === null) {
            return null;
        }

        $normalized = collect(explode('|', $rules))
            ->map(fn ($rule) => trim($rule))
            ->map(fn ($rule) => trim($rule, "` \t\n\r\0\x0B"))
            ->filter()
            ->implode('|');

        return $normalized !== '' ? $normalized : null;
    }

    protected function normalizeWorkflowSteps(Collection $steps): array
    {
        return $steps
            ->map(function (array $step) {
                return [
                    'name' => trim((string) ($step['name'] ?? '')),
                    'type' => $step['type'] ?? 'approval',
                    'assignee' => trim((string) ($step['assignee'] ?? '')),
                    'instructions' => trim((string) ($step['instructions'] ?? '')),
                    'sla_hours' => $step['sla_hours'] ?? null,
                ];
            })
            ->filter(fn (array $step) => collect($step)->except(['type'])->filter(fn ($value) => $value !== null && $value !== '')->isNotEmpty())
            ->values()
            ->all();
    }

    protected function normalizeNotificationRules(Collection $rules): array
    {
        return $rules
            ->map(function (array $rule) {
                return [
                    'label' => trim((string) ($rule['label'] ?? '')),
                    'trigger' => $rule['trigger'] ?? 'submitted',
                    'channel' => 'email',
                    'recipient' => trim((string) ($rule['recipient'] ?? '')),
                    'subject' => trim((string) ($rule['subject'] ?? '')),
                    'message_template' => trim((string) ($rule['message'] ?? '')),
                    'is_active' => (bool) ($rule['is_active'] ?? false),
                ];
            })
            ->filter(fn (array $rule) => collect($rule)->except(['trigger', 'channel', 'is_active'])->filter(fn ($value) => $value !== '')->isNotEmpty())
            ->values()
            ->all();
    }

    protected function normalizeAutomationActions(Collection $actions): array
    {
        return $actions
            ->map(function (array $action) {
                return [
                    'label' => trim((string) ($action['label'] ?? '')),
                    'trigger' => $action['trigger'] ?? 'completed',
                    'type' => $action['type'] ?? 'webhook',
                    'endpoint' => trim((string) ($action['endpoint'] ?? '')),
                    'method' => $action['method'] ?? 'POST',
                    'headers' => $this->normalizeHeaders($action['headers'] ?? ''),
                    'payload_template' => trim((string) ($action['payload'] ?? '')),
                    'is_active' => (bool) ($action['is_active'] ?? false),
                ];
            })
            ->filter(fn (array $action) => collect($action)->except(['trigger', 'type', 'method', 'headers', 'is_active'])->filter(fn ($value) => $value !== '')->isNotEmpty())
            ->values()
            ->all();
    }

    protected function normalizeHeaders(string $headers): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $headers))
            ->map(fn ($header) => trim($header))
            ->filter()
            ->mapWithKeys(function (string $header) {
                [$name, $value] = array_pad(explode(':', $header, 2), 2, '');

                return [trim($name) => trim($value)];
            })
            ->filter(fn ($value, $key) => $key !== '' && $value !== '')
            ->all();
    }

    protected function headersToLines(array $headers): string
    {
        return collect($headers)
            ->map(fn ($value, $key) => $key.': '.$value)
            ->implode(PHP_EOL);
    }

    protected function fieldTypes(): array
    {
        return [
            'text' => 'Text',
            'email' => 'Email',
            'number' => 'Number',
            'textarea' => 'Textarea',
            'select' => 'Select',
            'radio' => 'Radio',
            'checkbox' => 'Checkbox',
            'file' => 'File',
            'date' => 'Date',
        ];
    }

    protected function workflowStepTypes(): array
    {
        return [
            'review' => 'Review',
            'approval' => 'Approval',
        ];
    }

    protected function workflowTriggers(): array
    {
        return [
            'submitted',
            'step_waiting',
            'approved',
            'rejected',
            'completed',
        ];
    }

    protected function automationTypes(): array
    {
        return [
            'webhook' => 'Webhook',
            'api' => 'API call',
        ];
    }

    protected function automationMethods(): array
    {
        return ['POST', 'PUT', 'PATCH'];
    }
}
