<?php

namespace FomBuilder\DynamicForm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicFormSubmission extends Model
{
    use HasFactory;

    protected $table = 'dynamic_form_submissions';

    protected $fillable = [
        'form_id',
        'reference',
        'data',
        'status',
        'current_step',
        'current_step_name',
        'decision_notes',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'completed_at',
        'last_activity_at',
        'meta',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
        'meta' => 'array',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(DynamicForm::class, 'form_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DynamicFormSubmissionActivity::class, 'submission_id')->latest();
    }

    public function currentWorkflowStep(): ?array
    {
        $steps = $this->form?->workflowSteps() ?? [];

        return isset($steps[$this->current_step]) ? $steps[$this->current_step] : null;
    }

    public function isFinished(): bool
    {
        return in_array($this->status, ['approved', 'rejected', 'completed'], true);
    }
}
