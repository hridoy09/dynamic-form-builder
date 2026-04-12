<?php

namespace FomBuilder\DynamicForm\Services;

use FomBuilder\DynamicForm\Models\DynamicFormSubmission;

class DynamicFormWorkflowService
{
    public function __construct(
        protected DynamicFormSubmissionActivityService $activityService,
        protected DynamicFormAutomationService $automationService,
    ) {
    }

    public function initialize(DynamicFormSubmission $submission): DynamicFormSubmission
    {
        $submission->loadMissing('form');

        $this->ensureReference($submission);

        $this->activityService->log(
            $submission,
            'submission_received',
            'Submission received',
            'The submission entered the workflow engine.',
            ['status' => $submission->status],
            'submitted',
        );

        $this->automationService->runForTrigger($submission, 'submitted');

        $steps = $submission->form->workflowSteps();

        if ($steps === []) {
            $submission->forceFill([
                'status' => 'completed',
                'current_step' => null,
                'current_step_name' => null,
                'completed_at' => now(),
            ])->save();

            $this->activityService->log(
                $submission,
                'workflow_completed',
                'Workflow completed',
                'No manual approval steps were configured, so the submission completed immediately.',
                ['status' => $submission->status],
                'completed',
            );

            $this->automationService->runForTrigger($submission, 'completed');

            return $submission->fresh(['form', 'activities']);
        }

        $firstStep = $steps[0];

        $submission->forceFill([
            'status' => 'in_review',
            'current_step' => 0,
            'current_step_name' => $firstStep['name'],
            'completed_at' => null,
        ])->save();

        $this->activityService->log(
            $submission,
            'workflow_waiting',
            'Awaiting '.$firstStep['name'],
            $this->stepDescription($firstStep),
            ['step' => $firstStep],
            'step_waiting',
        );

        $this->automationService->runForTrigger($submission, 'step_waiting');

        return $submission->fresh(['form', 'activities']);
    }

    public function progress(
        DynamicFormSubmission $submission,
        string $action = 'approve',
        ?string $notes = null,
        ?string $actorName = null,
    ): DynamicFormSubmission {
        $submission->loadMissing('form');

        if ($submission->isFinished()) {
            return $submission;
        }

        if ($action === 'reject') {
            return $this->reject($submission, $notes, $actorName);
        }

        return $this->advance($submission, $notes, $actorName);
    }

    protected function advance(DynamicFormSubmission $submission, ?string $notes, ?string $actorName): DynamicFormSubmission
    {
        $steps = $submission->form->workflowSteps();
        $currentIndex = (int) ($submission->current_step ?? 0);
        $currentStep = $steps[$currentIndex] ?? null;

        $submission->forceFill([
            'reviewed_at' => now(),
            'decision_notes' => $notes ?: $submission->decision_notes,
        ])->save();

        $verb = ($currentStep['type'] ?? 'approval') === 'review' ? 'completed' : 'approved';

        $this->activityService->log(
            $submission,
            'workflow_progressed',
            ucfirst($verb).' '.$submission->current_step_name,
            $notes ?: 'The workflow moved forward to the next configured stage.',
            ['step' => $currentStep],
            'step_completed',
            $actorName,
        );

        $nextIndex = $currentIndex + 1;

        if (isset($steps[$nextIndex])) {
            $nextStep = $steps[$nextIndex];

            $submission->forceFill([
                'status' => 'in_review',
                'current_step' => $nextIndex,
                'current_step_name' => $nextStep['name'],
            ])->save();

            $this->activityService->log(
                $submission,
                'workflow_waiting',
                'Awaiting '.$nextStep['name'],
                $this->stepDescription($nextStep),
                ['step' => $nextStep],
                'step_waiting',
            );

            $this->automationService->runForTrigger($submission, 'step_waiting');

            return $submission->fresh(['form', 'activities']);
        }

        $finalStatus = ($currentStep['type'] ?? 'approval') === 'review' ? 'completed' : 'approved';

        $submission->forceFill([
            'status' => $finalStatus,
            'current_step' => null,
            'current_step_name' => null,
            'approved_at' => $finalStatus === 'approved' ? now() : $submission->approved_at,
            'completed_at' => now(),
        ])->save();

        $this->activityService->log(
            $submission,
            'workflow_completed',
            $finalStatus === 'approved' ? 'Submission approved' : 'Workflow completed',
            $notes ?: 'All workflow stages have been completed.',
            ['status' => $finalStatus],
            $finalStatus === 'approved' ? 'approved' : 'completed',
            $actorName,
        );

        if ($finalStatus === 'approved') {
            $this->automationService->runForTrigger($submission, 'approved');
        }

        $this->automationService->runForTrigger($submission, 'completed');

        return $submission->fresh(['form', 'activities']);
    }

    protected function reject(DynamicFormSubmission $submission, ?string $notes, ?string $actorName): DynamicFormSubmission
    {
        $submission->forceFill([
            'status' => 'rejected',
            'current_step' => null,
            'current_step_name' => null,
            'decision_notes' => $notes ?: $submission->decision_notes,
            'reviewed_at' => now(),
            'rejected_at' => now(),
        ])->save();

        $this->activityService->log(
            $submission,
            'workflow_rejected',
            'Submission rejected',
            $notes ?: 'The workflow was rejected and will not progress further.',
            ['status' => 'rejected'],
            'rejected',
            $actorName,
        );

        $this->automationService->runForTrigger($submission, 'rejected');

        return $submission->fresh(['form', 'activities']);
    }

    protected function ensureReference(DynamicFormSubmission $submission): void
    {
        if ($submission->reference) {
            return;
        }

        $submission->forceFill([
            'reference' => sprintf('SUB-%06d', $submission->id),
        ])->save();
    }

    protected function stepDescription(array $step): string
    {
        $parts = [];

        if (! empty($step['type'])) {
            $parts[] = ucfirst($step['type']).' step';
        }

        if (! empty($step['assignee'])) {
            $parts[] = 'Assigned to '.$step['assignee'];
        }

        if (! empty($step['sla_hours'])) {
            $parts[] = 'Target response in '.$step['sla_hours'].' hour(s)';
        }

        if (! empty($step['instructions'])) {
            $parts[] = $step['instructions'];
        }

        return $parts !== [] ? implode('. ', $parts).'.' : 'Awaiting manual workflow action.';
    }
}
