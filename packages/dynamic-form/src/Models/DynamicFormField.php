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

    public function optionsList(): array
    {
        return array_values(array_filter($this->options ?? [], fn ($value) => $value !== null && $value !== ''));
    }
}
