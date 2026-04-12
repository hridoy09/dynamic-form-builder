<?php

namespace FomBuilder\DynamicForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicFormSubmissionActivity extends Model
{
    use HasFactory;

    protected $table = 'dynamic_form_submission_activities';

    protected $fillable = [
        'submission_id',
        'type',
        'title',
        'description',
        'trigger',
        'actor_name',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(DynamicFormSubmission::class, 'submission_id');
    }
}
