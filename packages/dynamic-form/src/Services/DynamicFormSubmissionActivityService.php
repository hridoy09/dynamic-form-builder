<?php

namespace FomBuilder\DynamicForm\Services;

use FomBuilder\DynamicForm\Models\DynamicFormSubmission;
use FomBuilder\DynamicForm\Models\DynamicFormSubmissionActivity;

class DynamicFormSubmissionActivityService
{
    public function log(
        DynamicFormSubmission $submission,
        string $type,
        string $title,
        ?string $description = null,
        array $payload = [],
        ?string $trigger = null,
        ?string $actorName = null,
    ): DynamicFormSubmissionActivity {
        $activity = $submission->activities()->create([
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'trigger' => $trigger,
            'actor_name' => $actorName,
            'payload' => $payload !== [] ? $payload : null,
        ]);

        $submission->forceFill([
            'last_activity_at' => now(),
        ])->save();

        return $activity;
    }
}
