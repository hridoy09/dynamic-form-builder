<?php

namespace FomBuilder\DynamicForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicForm extends Model
{
    use HasFactory;

    protected $table = 'dynamic_forms';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'submit_label',
        'success_message',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(DynamicFormField::class, 'form_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(DynamicFormSubmission::class, 'form_id')->latest();
    }
}
