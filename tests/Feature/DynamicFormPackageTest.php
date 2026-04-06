<?php

namespace Tests\Feature;

use FomBuilder\DynamicForm\Models\DynamicForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DynamicFormPackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_builder_page_is_available(): void
    {
        $this->get('/dynamic-forms')
            ->assertOk()
            ->assertSee('Dynamic Form Builder');
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
        Storage::disk('public')->assertExists($submission->data['resume']['path']);
    }
}
