# Dynamic Form

A Laravel package for building dynamic forms with a clean admin builder, Blade rendering, validation, submission storage, file uploads, and submission management.

## Overview

Dynamic Form helps you create and manage forms without hardcoding each field by hand. Create forms from a builder UI, render them in Blade with a single directive, and collect submissions directly in your Laravel application.

## Features

- Visual form builder
- Dynamic field definitions
- Built-in Laravel validation support
- Blade directive rendering
- Submission storage in database
- File upload handling
- Submission list and review screen
- Configurable route prefixes and storage disk

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
];
```

Environment example:

```env
DYNAMIC_FORM_STORAGE_DISK=public
```

## Routes

### Builder routes

- `GET /dynamic-forms`
- `GET /dynamic-forms/create`
- `POST /dynamic-forms`
- `GET /dynamic-forms/{form}/edit`
- `PUT /dynamic-forms/{form}`
- `GET /dynamic-forms/{form}/submissions`

### Public routes

- `GET /forms/{slug}`
- `POST /forms/{slug}`

## Quick Start

1. Visit `/dynamic-forms`
2. Click `Create form`
3. Add form details such as name, slug, button label, and success message
4. Add your fields and validation rules
5. Save the form
6. Open the public form URL using the slug
7. Submit the form and review responses from the submissions page

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

## Field Options

Each field can include:

- Label
- Field name
- Type
- Placeholder
- Help text
- Options
- Validation rules
- Required status
- Sort order

For `select`, `radio`, and multi-value `checkbox`, add one option per line.

Example:

```text
Basic
Standard
Premium
```

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

## Rendering in Blade

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
- Submitted data
- Uploaded file metadata
- IP address
- User agent
- Created timestamp

To view submissions:

1. Open `/dynamic-forms`
2. Select a form
3. Click `Submissions`

## Example Use Cases

### Contact Form

Fields:

- `name` as `text`
- `email` as `email`
- `message` as `textarea`

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

Validation:

```text
full_name: required|max:255
email: required|email|max:255
resume: required|mimes:pdf,doc,docx|max:2048
```

## Testing

Run the package test:

```bash
php artisan test --filter=DynamicFormPackageTest
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
