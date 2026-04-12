<?php

namespace Tests\Feature;

use FomBuilder\DynamicForm\Models\DynamicForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DynamicFormPackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_builder_page_is_available(): void
    {
        $this->get('/dynamic-forms')
            ->assertOk()
            ->assertSee('Dynamic Form + Workflow Builder');
    }

    public function test_form_submission_is_saved_with_uploaded_file(): void
    {
        Storage::fake('public');

        $form = DynamicForm::query()->create([
            'name' => 'Job Application',
            'slug' => 'job-application',
            'submit_label' => 'Apply now',
            'success_message' => 'Submitted',
            'is_active' => true,
        ]);

        $form->fields()->createMany([
            [
                'label' => 'Full name',
                'name' => 'full_name',
                'type' => 'text',
                'validation' => 'max:255',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'label' => 'Resume',
                'name' => 'resume',
                'type' => 'file',
                'validation' => 'mimes:pdf|max:2048',
                'is_required' => true,
                'sort_order' => 2,
            ],
        ]);

        $response = $this->post('/forms/job-application', [
            'full_name' => 'Jane Doe',
            'resume' => UploadedFile::fake()->create('resume.pdf', 120, 'application/pdf'),
        ]);

        $response->assertRedirect('/forms/job-application');
        $this->assertDatabaseCount('dynamic_form_submissions', 1);

        $submission = $form->submissions()->first();

        $this->assertSame('Jane Doe', $submission->data['full_name']);
        $this->assertSame('resume.pdf', $submission->data['resume']['original_name']);
        $this->assertSame('completed', $submission->status);
        $this->assertNotNull($submission->reference);
        Storage::disk('public')->assertExists($submission->data['resume']['path']);
    }

    public function test_submission_can_move_through_approval_flow_and_fire_automation(): void
    {
        Mail::fake();
        Http::fake([
            'https://hooks.example.test/forms' => Http::response(['ok' => true], 202),
        ]);

        $form = DynamicForm::query()->create([
            'name' => 'Vendor onboarding',
            'slug' => 'vendor-onboarding',
            'submit_label' => 'Send',
            'success_message' => 'Submitted',
            'workflow_definition' => [
                [
                    'name' => 'Manager approval',
                    'type' => 'approval',
                    'assignee' => 'Team lead',
                    'instructions' => 'Validate the submission before approval.',
                    'sla_hours' => 24,
                ],
            ],
            'notification_settings' => [
                [
                    'label' => 'Ops inbox',
                    'trigger' => 'submitted',
                    'channel' => 'email',
                    'recipient' => 'ops@example.com',
                    'subject' => 'New {{ form.name }} submission',
                    'message_template' => 'Submission {{ submission.reference }} arrived.',
                    'is_active' => true,
                ],
            ],
            'automation_settings' => [
                [
                    'label' => 'Approved webhook',
                    'trigger' => 'approved',
                    'type' => 'webhook',
                    'endpoint' => 'https://hooks.example.test/forms',
                    'method' => 'POST',
                    'headers' => ['X-Workflow' => 'dynamic-form'],
                    'payload_template' => '{"reference":"{{ submission.reference }}","status":"{{ submission.status }}"}',
                    'is_active' => true,
                ],
            ],
            'is_active' => true,
        ]);

        $form->fields()->create([
            'label' => 'Company name',
            'name' => 'company_name',
            'type' => 'text',
            'validation' => 'max:255',
            'is_required' => true,
            'sort_order' => 1,
        ]);

        $response = $this->post('/forms/vendor-onboarding', [
            'company_name' => 'Acme Labs',
        ]);

        $response->assertRedirect('/forms/vendor-onboarding');

        $submission = $form->submissions()->first()->fresh(['activities', 'form']);

        $this->assertSame('in_review', $submission->status);
        $this->assertSame('Manager approval', $submission->current_step_name);
        $this->assertSame('Acme Labs', $submission->data['company_name']);
        $this->assertStringStartsWith('SUB-', $submission->reference);
        $this->assertDatabaseHas('dynamic_form_submission_activities', [
            'submission_id' => $submission->id,
            'type' => 'notification_sent',
            'trigger' => 'submitted',
        ]);

        $approveResponse = $this->patch("/dynamic-forms/{$form->id}/submissions/{$submission->id}", [
            'action' => 'approve',
            'notes' => 'Looks good.',
        ]);

        $approveResponse->assertRedirect("/dynamic-forms/{$form->id}/submissions/{$submission->id}");

        $submission->refresh();

        $this->assertSame('approved', $submission->status);
        $this->assertNull($submission->current_step_name);
        $this->assertNotNull($submission->approved_at);
        $this->assertDatabaseHas('dynamic_form_submission_activities', [
            'submission_id' => $submission->id,
            'type' => 'automation_sent',
            'trigger' => 'approved',
        ]);

        Http::assertSent(function ($request) use ($submission) {
            return $request->url() === 'https://hooks.example.test/forms'
                && $request->hasHeader('X-Workflow', 'dynamic-form')
                && $request['reference'] === $submission->reference
                && $request['status'] === 'approved';
        });
    }

    public function test_select_field_can_use_label_and_value_options(): void
    {
        $form = DynamicForm::query()->create([
            'name' => 'Plan selection',
            'slug' => 'plan-selection',
            'submit_label' => 'Choose',
            'success_message' => 'Saved',
            'is_active' => true,
        ]);

        $form->fields()->create([
            'label' => 'Plan',
            'name' => 'plan',
            'type' => 'select',
            'options' => [
                ['label' => 'Starter Plan', 'value' => 'starter'],
                ['label' => 'Growth Plan', 'value' => 'growth'],
            ],
            'is_required' => true,
            'sort_order' => 1,
        ]);

        $this->get('/forms/plan-selection')
            ->assertOk()
            ->assertSee('Starter Plan')
            ->assertSee('Growth Plan');

        $response = $this->post('/forms/plan-selection', [
            'plan' => 'growth',
        ]);

        $response->assertRedirect('/forms/plan-selection');

        $submission = $form->submissions()->first();

        $this->assertSame('growth', $submission->data['plan']);
    }
}
