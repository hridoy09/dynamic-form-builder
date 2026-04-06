# Dynamic Form

`fombuilder/dynamic-form` is a Laravel package for building forms from a UI, rendering them in Blade, validating user input, saving submissions, and handling file uploads.

## Features

- Form builder UI
- Dynamic fields
- Laravel validation rules per field
- Blade rendering with `@dynamicForm('slug')`
- Submission saving
- File upload support
- Submission listing

## Requirements

- PHP 8.3 or newer
- Laravel 13 or newer

## Installation

You can use this package in 2 ways.

### Option 1: Install From Packagist

If the package is already published to Packagist:

```bash
composer require fombuilder/dynamic-form
```

### Option 2: Install As A Local Path Package

If the package is not published yet and you want to use it from a local folder:

1. Copy the package into your Laravel project or keep it in a separate local directory.
2. Add a path repository to your Laravel app `composer.json`:

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

3. Require the package:

```bash
composer require fombuilder/dynamic-form:*
```

If your package is outside the project, update the `url` path to match your folder.

## Laravel Auto Discovery

This package supports Laravel package discovery, so you do not need to manually register the service provider when installed through Composer.

Service provider:

```php
FomBuilder\DynamicForm\DynamicFormServiceProvider::class
```

## Publish Assets

Publish the config file:

```bash
php artisan vendor:publish --tag=dynamic-form-config
```

Publish the views if you want to customize the package UI:

```bash
php artisan vendor:publish --tag=dynamic-form-views
```

## Run Migrations

Create the package tables:

```bash
php artisan migrate
```

This package creates tables for:

- forms
- form fields
- form submissions

## File Upload Setup

If you are using the `public` disk, create the storage symlink:

```bash
php artisan storage:link
```

You can also set the upload disk in your `.env`:

```env
DYNAMIC_FORM_STORAGE_DISK=public
```

## Configuration

After publishing the config, you can edit:

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

## Routes

### Builder Routes

These routes are used for creating and managing forms:

- `GET /dynamic-forms`
- `GET /dynamic-forms/create`
- `POST /dynamic-forms`
- `GET /dynamic-forms/{form}/edit`
- `PUT /dynamic-forms/{form}`
- `GET /dynamic-forms/{form}/submissions`

### Public Routes

These routes are used for displaying and submitting forms:

- `GET /forms/{slug}`
- `POST /forms/{slug}`

If you change the route prefixes in config, the URLs will change too.

## How To Create A Form

1. Open your browser.
2. Visit:

```text
/dynamic-forms
```

3. Click `Create form`.
4. Fill in:
   - form name
   - slug
   - description
   - submit button label
   - success message
5. Add fields.
6. Save the form.

## Available Field Types

The builder currently supports:

- `text`
- `email`
- `number`
- `textarea`
- `select`
- `radio`
- `checkbox`
- `file`
- `date`

## Field Settings

Each field can have:

- label
- field name
- type
- placeholder
- help text
- options
- validation rules
- required flag
- sort order

### Options

For `select`, `radio`, and multi-option `checkbox` fields, enter one option per line.

Example:

```text
Male
Female
Other
```

## Validation Rules

The `Validation` input accepts normal Laravel validation rules.

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

## Render A Form In Blade

You can render any active form in a Blade view using its slug:

```blade
@dynamicForm('contact-form')
```

Example inside a Blade page:

```blade
@extends('layouts.app')

@section('content')
    <h1>Contact Us</h1>

    @dynamicForm('contact-form')
@endsection
```

## Public Form Usage

If your form slug is `contact-form`, the public form URL will be:

```text
/forms/contact-form
```

Users can open that page and submit the form.

## Submission Storage

Each submission is stored in the database.

The package stores:

- form relation
- submitted field values
- uploaded file metadata
- IP address
- user agent
- timestamps

## View Submissions

To view submissions for a form:

1. Open the builder page.
2. Select the form.
3. Click `Submissions`.

You will see all saved entries for that form.

## Example Workflow

### Example 1: Contact Form

Create a form with:

- `name` as `text`
- `email` as `email`
- `message` as `textarea`

Validation examples:

```text
name: required|max:255
email: required|email|max:255
message: required|min:10|max:2000
```

Then render it using:

```blade
@dynamicForm('contact-form')
```

### Example 2: Job Application Form

Create a form with:

- `full_name` as `text`
- `email` as `email`
- `resume` as `file`

Validation examples:

```text
full_name: required|max:255
email: required|email|max:255
resume: required|mimes:pdf,doc,docx|max:2048
```

## Customizing Views

If you publish the views:

```bash
php artisan vendor:publish --tag=dynamic-form-views
```

You can edit the published Blade files in your application and customize the UI.

## Testing

Run the package test with:

```bash
php artisan test --filter=DynamicFormPackageTest
```

## Troubleshooting

### `could not find driver`

This usually means your test environment is configured for SQLite but the `pdo_sqlite` extension is not enabled in PHP.

Check loaded PHP modules:

```bash
php -m
```

### `Method Illuminate\Validation\Validator::validate`email` does not exist`

This happens when a validation rule was entered with backticks.

Wrong:

```text
`email`
```

Correct:

```text
email
```

### Uploaded files are not opening

Make sure you ran:

```bash
php artisan storage:link
```

and that your disk is configured correctly.

## Package Structure

```text
config/
database/
resources/
routes/
src/
README.md
composer.json
```

## License

MIT
