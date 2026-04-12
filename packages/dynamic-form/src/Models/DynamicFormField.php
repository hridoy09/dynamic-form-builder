<?php

namespace FomBuilder\DynamicForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicFormField extends Model
{
    use HasFactory;

    protected $table = 'dynamic_form_fields';

    protected $fillable = [
        'form_id',
        'label',
        'name',
        'type',
        'placeholder',
        'help_text',
        'options',
        'validation',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(DynamicForm::class, 'form_id');
    }

    public function normalizedOptions(): array
    {
        return collect($this->options ?? [])
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

    public function optionsList(): array
    {
        return collect($this->normalizedOptions())
            ->pluck('value')
            ->values()
            ->all();
    }
}
