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
            ->get();

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
            'fields' => $form->fields->map(fn ($field) => [
                'label' => $field->label,
                'name' => $field->name,
                'type' => $field->type,
                'placeholder' => $field->placeholder,
                'help_text' => $field->help_text,
                'options' => implode(PHP_EOL, $field->optionsList()),
                'validation' => $field->validation,
                'is_required' => $field->is_required,
                'sort_order' => $field->sort_order,
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
                    'options' => collect(preg_split('/\r\n|\r|\n/', (string) ($field['options'] ?? '')))
                        ->map(fn ($option) => trim($option))
                        ->filter()
                        ->values()
                        ->all(),
                    'validation' => $this->normalizeValidationRules($field['validation'] ?? null),
                    'is_required' => (bool) ($field['is_required'] ?? false),
                    'sort_order' => $field['sort_order'] ?? ($index + 1),
                ];
            })
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
}
