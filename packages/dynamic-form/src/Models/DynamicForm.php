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
        'workflow_definition',
        'notification_settings',
        'automation_settings',
        'is_active',
    ];

    protected $casts = [
        'workflow_definition' => 'array',
        'notification_settings' => 'array',
        'automation_settings' => 'array',
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

    public function workflowSteps(): array
    {
        return $this->configRows($this->workflow_definition);
    }

    public function notificationRules(): array
    {
        return $this->configRows($this->notification_settings);
    }

    public function automationActions(): array
    {
        return $this->configRows($this->automation_settings);
    }

    protected function configRows(?array $rows): array
    {
        return collect($rows ?? [])
            ->filter(fn ($row) => is_array($row))
            ->values()
            ->all();
    }
}
