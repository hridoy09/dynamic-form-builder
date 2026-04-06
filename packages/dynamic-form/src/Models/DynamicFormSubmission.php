<?php

namespace FomBuilder\DynamicForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicFormSubmission extends Model
{
    use HasFactory;

    protected $table = 'dynamic_form_submissions';

    protected $fillable = [
        'form_id',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(DynamicForm::class, 'form_id');
    }
}
