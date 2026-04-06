<?php

namespace FomBuilder\DynamicForm\Services;

use FomBuilder\DynamicForm\Models\DynamicForm;
use FomBuilder\DynamicForm\Models\DynamicFormField;
use FomBuilder\DynamicForm\Models\DynamicFormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DynamicFormSubmissionService
{
    public function validate(Request $request, DynamicForm $form): array
    {
        $form->loadMissing('fields');

        return Validator::make($request->all(), $this->rules($form))->validate();
    }

    public function submit(Request $request, DynamicForm $form): DynamicFormSubmission
    {
        $validated = $this->validate($request, $form);
        $payload = [];
        $disk = config('dynamic-form.storage_disk', 'public');

        foreach ($form->fields as $field) {
            if ($field->type === 'file') {
                if ($request->hasFile($field->name)) {
                    $file = $request->file($field->name);
                    $path = $file->store(
                        trim(config('dynamic-form.upload_directory', 'dynamic-forms'), '/').'/'.$form->id,
                        $disk
                    );

                    $payload[$field->name] = [
                        'disk' => $disk,
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                    ];
                }

                continue;
            }

            $payload[$field->name] = Arr::get($validated, $field->name);
        }

        return $form->submissions()->create([
            'data' => $payload,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);
    }

    public function rules(DynamicForm $form): array
    {
        $rules = [];

        foreach ($form->fields as $field) {
            $baseRules = $this->baseRules($field);

            if ($field->type === 'checkbox' && $field->optionsList() !== []) {
                $rules[$field->name] = [...$baseRules, 'array'];
                $rules[$field->name.'.*'] = ['string', Rule::in($field->optionsList())];
                continue;
            }

            $rules[$field->name] = $baseRules;
        }

        return $rules;
    }

    protected function baseRules(DynamicFormField $field): array
    {
        $rules = [$field->is_required ? 'required' : 'nullable'];

        $typeRules = match ($field->type) {
            'email' => ['string', 'email'],
            'number' => ['numeric'],
            'date' => ['date'],
            'textarea', 'text' => ['string'],
            'select', 'radio' => $field->optionsList() === [] ? ['string'] : ['string', Rule::in($field->optionsList())],
            'checkbox' => $field->optionsList() === [] ? ['boolean'] : [],
            'file' => ['file'],
            default => ['string'],
        };

        $customRules = collect(explode('|', (string) $field->validation))
            ->map(fn ($rule) => trim($rule))
            ->map(fn ($rule) => trim($rule, "` \t\n\r\0\x0B"))
            ->filter()
            ->values()
            ->all();

        return [...$rules, ...$typeRules, ...$customRules];
    }
}
