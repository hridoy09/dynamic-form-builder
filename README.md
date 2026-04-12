# Dynamic Form + Workflow Builder

A Laravel package for building production-ready forms with a polished admin builder, submission handling, approval workflow, notifications, automations, file uploads, and Blade rendering.

## Overview

Dynamic Form + Workflow Builder helps you build more than just input fields. It gives you the full submission lifecycle:

- Form builder
- Public form rendering
- Validation
- Submission storage
- Approval flow
- Notifications
- API and webhook automations
- Submission review timeline

This makes the package feel like a real internal tool or SaaS-style builder instead of a basic form generator.

## Features

- Professional form builder UI
- Dynamic field definitions
- Label and value based option builder for `select`, `radio`, and `checkbox`
- Built-in Laravel validation support
- Blade directive rendering with `@dynamicForm()`
- Submission storage in database
- File upload handling
- Workflow stages for review and approval
- Submission status tracking
- Submission activity timeline
- Email notification rules
- Webhook and API-style automation actions
- Dedicated submission review screen with approve and reject actions
- Configurable route prefixes, storage disk, and webhook timeout

## Requirements

- PHP 8.3+
- Laravel 13+

## Installation

### Install from Packagist

```bash
composer require fombuilder/dynamic-form
```

### Install as a local path package

Add this to your application's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/dynamic-form",
            "options": {
                "symlink": true
            }
        }
    ]
}
```

Then require the package:

```bash
composer require fombuilder/dynamic-form:*
```

## Setup

Run the package migrations:

```bash
php artisan migrate
```

Create the storage symlink for uploaded files:

```bash
php artisan storage:link
```

Publish the config file if you want to customize settings:

```bash
php artisan vendor:publish --tag=dynamic-form-config
```

Publish the package views if you want to customize the UI:

```bash
php artisan vendor:publish --tag=dynamic-form-views
```

## Configuration

Configuration file:

```php
config/dynamic-form.php
```

Default configuration:

```php
return [
    'route_prefix' => 'dynamic-forms',
    'route_middleware' => ['web'],
    'public_route_prefix' => 'forms',
    'public_route_middleware' => ['web'],
    'storage_disk' => env('DYNAMIC_FORM_STORAGE_DISK', 'public'),
    'upload_directory' => 'dynamic-forms',
    'webhook_timeout' => (int) env('DYNAMIC_FORM_WEBHOOK_TIMEOUT', 10),
    'notification_from_address' => env('DYNAMIC_FORM_NOTIFICATION_FROM_ADDRESS'),
    'notification_from_name' => env('DYNAMIC_FORM_NOTIFICATION_FROM_NAME', env('APP_NAME', 'Laravel')),
];
```

Environment example:

```env
DYNAMIC_FORM_STORAGE_DISK=public
DYNAMIC_FORM_WEBHOOK_TIMEOUT=10
DYNAMIC_FORM_NOTIFICATION_FROM_ADDRESS=no-reply@example.com
DYNAMIC_FORM_NOTIFICATION_FROM_NAME="Form Builder"
```

## Routes

### Builder routes

- `GET /dynamic-forms`
- `GET /dynamic-forms/create`
- `POST /dynamic-forms`
- `GET /dynamic-forms/{form}/edit`
- `PUT /dynamic-forms/{form}`
- `GET /dynamic-forms/{form}/submissions`
- `GET /dynamic-forms/{form}/submissions/{submission}`
- `PATCH /dynamic-forms/{form}/submissions/{submission}`

### Public routes

- `GET /forms/{slug}`
- `POST /forms/{slug}`

## Quick Start

1. Visit `/dynamic-forms`
2. Click `Create form`
3. Add form details such as name, slug, button label, and success message
4. Add your fields and validation rules
5. If the field type is `select`, `radio`, or `checkbox`, use the option builder modal to add option name and value pairs
6. Add workflow stages such as review or approval
7. Add notification rules if your team should receive email updates
8. Add automation actions if you want to call a webhook or external API
9. Save the form
10. Open the public form URL using the slug
11. Submit the form and review the workflow timeline from the submissions area

## Available Field Types

- `text`
- `email`
- `number`
- `textarea`
- `select`
- `radio`
- `checkbox`
- `file`
- `date`

## Field Builder

Each field can include:

- Label
- Field name
- Type
- Placeholder
- Help text
- Validation rules
- Required status
- Sort order
- Options for `select`, `radio`, and `checkbox`

### Option Builder

For `select`, `radio`, and multi-value `checkbox`, the builder shows an `Add option` button. Clicking it opens a modal where you can enter:

- Option name
- Option value

Example:

```text
Option name: Premium Plan
Option value: premium-plan
```

The option name is what users see in the form. The option value is what gets stored in the submission data.

## Workflow Builder

Each form can include multiple workflow stages.

Supported stage types:

- `review`
- `approval`

Each stage can include:

- Stage name
- Stage type
- Owner or assignee
- Instructions
- SLA target in hours

Example workflow:

1. Operations review
2. Manager approval

If no workflow stages are configured, submissions complete automatically after they are saved.

## Notifications

You can add email notification rules in the builder.

Each rule can include:

- Label
- Trigger
- Recipient list
- Subject
- Message template
- Active status

Supported triggers:

- `submitted`
- `step_waiting`
- `approved`
- `rejected`
- `completed`

Available template tokens:

- `{{ form.name }}`
- `{{ form.slug }}`
- `{{ submission.id }}`
- `{{ submission.reference }}`
- `{{ submission.status }}`
- `{{ current_step.name }}`
- `{{ current_step.type }}`

## Automations

You can connect forms to downstream systems with automation actions.

Each automation can include:

- Label
- Trigger
- Action type
- Endpoint URL
- HTTP method
- Custom headers
- JSON payload template
- Active status

Current supported action types:

- `webhook`
- `api`

Supported methods:

- `POST`
- `PUT`
- `PATCH`

## Validation Rules

The validation input accepts normal Laravel validation rules.

Examples:

```text
required
max:255
required|email|max:255
nullable|mimes:pdf,jpg,png|max:2048
required|min:10|max:500
```

Do not wrap rules in backticks.

Wrong:

```text
`email`
```

Correct:

```text
email
```

## Rendering In Blade

Render any active form by slug:

```blade
@dynamicForm('contact-form')
```

Example:

```blade
@extends('layouts.app')

@section('content')
    <h1>Contact Us</h1>

    @dynamicForm('contact-form')
@endsection
```

## Public Form Example

If your form slug is `contact-form`, the public URL will be:

```text
/forms/contact-form
```

## Submission Management

Each submission stores:

- Form reference
- Submission reference like `SUB-000001`
- Submitted data
- Uploaded file metadata
- Current status
- Current workflow step
- Decision notes
- IP address
- User agent
- Review, approval, rejection, and completion timestamps
- Submission activity timeline

Submission statuses include:

- `submitted`
- `in_review`
- `approved`
- `rejected`
- `completed`

From the admin panel you can:

1. Open a form
2. Review recent submissions
3. Open a submission detail page
4. Approve or reject workflow stages
5. Inspect notification and automation events from the timeline

## Example Use Cases

### Contact Form

Fields:

- `name` as `text`
- `email` as `email`
- `message` as `textarea`

Workflow:

- Team review

Validation:

```text
name: required|max:255
email: required|email|max:255
message: required|min:10|max:2000
```

### Job Application Form

Fields:

- `full_name` as `text`
- `email` as `email`
- `resume` as `file`
- `department` as `select`

Workflow:

- HR review
- Hiring manager approval

Validation:

```text
full_name: required|max:255
email: required|email|max:255
resume: required|mimes:pdf,doc,docx|max:2048
department: required
```

### Vendor Onboarding Form

Fields:

- `company_name` as `text`
- `contact_email` as `email`
- `plan` as `select`

Workflow:

- Operations review
- Finance approval

Automation:

- Send approved vendor payload to a webhook endpoint

## Testing

Run the package-specific test:

```bash
php artisan test --filter=DynamicFormPackageTest
```

Run the full test suite:

```bash
php artisan test
```

## Troubleshooting

### SQLite driver error

If you see `could not find driver`, your PHP CLI likely does not have `pdo_sqlite` enabled.

Check loaded modules:

```bash
php -m
```

### Validation method error

If you see an error like `Method Illuminate\\Validation\\Validator::validate`email` does not exist`, a validation rule was entered with backticks.

Use `email`, not `` `email` ``.

### Uploaded files are not accessible

Make sure the storage symlink exists:

```bash
php artisan storage:link
```

### Notifications are not sending

Make sure your Laravel mail configuration is working and, if needed, set:

```env
DYNAMIC_FORM_NOTIFICATION_FROM_ADDRESS=no-reply@example.com
DYNAMIC_FORM_NOTIFICATION_FROM_NAME="Form Builder"
```

### Webhook calls are timing out

Increase the timeout in your environment:

```env
DYNAMIC_FORM_WEBHOOK_TIMEOUT=20
```

## Package Structure

```text
config/
database/
resources/
routes/
src/
composer.json
README.md
```

## License

MIT
