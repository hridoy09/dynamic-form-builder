<?php

namespace FomBuilder\DynamicForm\Services;

use FomBuilder\DynamicForm\Models\DynamicFormSubmission;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Throwable;

class DynamicFormAutomationService
{
    public function __construct(
        protected DynamicFormSubmissionActivityService $activityService,
    ) {
    }

    public function runForTrigger(DynamicFormSubmission $submission, string $trigger): void
    {
        $submission->loadMissing('form');

        foreach ($submission->form->notificationRules() as $notification) {
            if (($notification['is_active'] ?? true) && ($notification['trigger'] ?? 'submitted') === $trigger) {
                $this->sendNotification($submission, $notification, $trigger);
            }
        }

        foreach ($submission->form->automationActions() as $automation) {
            if (($automation['is_active'] ?? true) && ($automation['trigger'] ?? 'completed') === $trigger) {
                $this->sendAutomation($submission, $automation, $trigger);
            }
        }
    }

    protected function sendNotification(DynamicFormSubmission $submission, array $notification, string $trigger): void
    {
        $recipients = collect(preg_split('/[\r\n,]+/', (string) ($notification['recipient'] ?? '')))
            ->map(fn ($recipient) => trim($recipient))
            ->filter()
            ->values()
            ->all();

        if ($recipients === []) {
            $this->activityService->log(
                $submission,
                'notification_skipped',
                ($notification['label'] ?? 'Notification').' skipped',
                'No recipient has been configured for this notification rule.',
                ['rule' => $notification],
                $trigger,
            );

            return;
        }

        $subject = $this->interpolate(
            $notification['subject'] ?? 'New submission for {{ form.name }}',
            $submission,
        );

        $body = $this->interpolate(
            $notification['message_template'] ?? $this->defaultNotificationBody($submission),
            $submission,
        );

        try {
            Mail::raw($body, function ($message) use ($recipients, $subject): void {
                $message->to($recipients)->subject($subject);

                if ($from = config('dynamic-form.notification_from_address')) {
                    $message->from($from, config('dynamic-form.notification_from_name'));
                }
            });

            $this->activityService->log(
                $submission,
                'notification_sent',
                ($notification['label'] ?? 'Notification').' sent',
                'Email notification sent to '.implode(', ', $recipients).'.',
                [
                    'channel' => $notification['channel'] ?? 'email',
                    'recipients' => $recipients,
                    'subject' => $subject,
                ],
                $trigger,
            );
        } catch (Throwable $exception) {
            report($exception);

            $this->activityService->log(
                $submission,
                'notification_failed',
                ($notification['label'] ?? 'Notification').' failed',
                $exception->getMessage(),
                [
                    'channel' => $notification['channel'] ?? 'email',
                    'recipients' => $recipients,
                    'subject' => $subject,
                ],
                $trigger,
            );
        }
    }

    protected function sendAutomation(DynamicFormSubmission $submission, array $automation, string $trigger): void
    {
        $endpoint = trim((string) ($automation['endpoint'] ?? ''));

        if ($endpoint === '') {
            $this->activityService->log(
                $submission,
                'automation_skipped',
                ($automation['label'] ?? 'Automation').' skipped',
                'No endpoint has been configured for this automation.',
                ['automation' => $automation],
                $trigger,
            );

            return;
        }

        $payload = $this->payloadForAutomation($submission, $automation);
        $method = strtoupper((string) ($automation['method'] ?? 'POST'));
        $headers = $automation['headers'] ?? [];

        try {
            $response = Http::timeout((int) config('dynamic-form.webhook_timeout', 10))
                ->withHeaders($headers)
                ->send($method, $endpoint, ['json' => $payload]);

            $this->activityService->log(
                $submission,
                $response->successful() ? 'automation_sent' : 'automation_failed',
                ($automation['label'] ?? 'Automation').' '.($response->successful() ? 'sent' : 'returned an error'),
                'HTTP '.$response->status().' response from '.$endpoint.'.',
                [
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'status' => $response->status(),
                ],
                $trigger,
            );
        } catch (Throwable $exception) {
            report($exception);

            $this->activityService->log(
                $submission,
                'automation_failed',
                ($automation['label'] ?? 'Automation').' failed',
                $exception->getMessage(),
                [
                    'endpoint' => $endpoint,
                    'method' => $method,
                ],
                $trigger,
            );
        }
    }

    protected function payloadForAutomation(DynamicFormSubmission $submission, array $automation): array
    {
        $template = trim((string) ($automation['payload_template'] ?? ''));

        if ($template !== '') {
            $interpolated = $this->interpolate($template, $submission);
            $decoded = json_decode($interpolated, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return [
            'form' => [
                'id' => $submission->form?->id,
                'name' => $submission->form?->name,
                'slug' => $submission->form?->slug,
            ],
            'submission' => [
                'id' => $submission->id,
                'reference' => $submission->reference,
                'status' => $submission->status,
                'current_step' => $submission->current_step_name,
                'submitted_at' => optional($submission->created_at)->toIso8601String(),
                'data' => $submission->data,
            ],
        ];
    }

    protected function defaultNotificationBody(DynamicFormSubmission $submission): string
    {
        $lines = [
            'Form: '.$submission->form?->name,
            'Submission: '.$submission->reference,
            'Status: '.$submission->status,
        ];

        if ($submission->current_step_name) {
            $lines[] = 'Current step: '.$submission->current_step_name;
        }

        $lines[] = '';
        $lines[] = 'Submitted data:';

        foreach ($submission->data ?? [] as $key => $value) {
            if (is_array($value)) {
                $value = Arr::get($value, 'original_name', json_encode($value));
            }

            $lines[] = '- '.$key.': '.$value;
        }

        return implode(PHP_EOL, $lines);
    }

    protected function interpolate(string $value, DynamicFormSubmission $submission): string
    {
        $step = $submission->currentWorkflowStep();

        return strtr($value, [
            '{{ form.id }}' => (string) $submission->form?->id,
            '{{ form.name }}' => (string) $submission->form?->name,
            '{{ form.slug }}' => (string) $submission->form?->slug,
            '{{ submission.id }}' => (string) $submission->id,
            '{{ submission.reference }}' => (string) $submission->reference,
            '{{ submission.status }}' => (string) $submission->status,
            '{{ current_step.name }}' => (string) ($submission->current_step_name ?? ''),
            '{{ current_step.type }}' => (string) ($step['type'] ?? ''),
        ]);
    }
}
