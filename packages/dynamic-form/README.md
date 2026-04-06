# Dynamic Form Package

`fombuilder/dynamic-form` is a Laravel package for creating dynamic forms with a builder UI, rendering them in Blade, validating submissions, saving responses, and handling file uploads.

## Features

- Create and edit forms from a builder page
- Add dynamic fields such as text, email, number, textarea, select, radio, checkbox, file, and date
- Apply Laravel validation rules per field
- Render forms in Blade with `@dynamicForm('slug')`
- Save submissions in the database
- Upload and store files
- View submission history for each form

## Requirements

- PHP 8.3+
- Laravel 13+

## Installation

Install the package with Composer:

```bash
composer require fombuilder/dynamic-form
```

Run migrations:

```bash
php artisan migrate
```

Create the public storage symlink for uploaded files:

```bash
php artisan storage:link
```

Optional: publish the config file:

```bash
php artisan vendor:publish --tag=dynamic-form-config
```

Optional: publish the views if you want to customize the UI:

```bash
php artisan vendor:publish --tag=dynamic-form-views
```

## Quick Start

1. Visit `/dynamic-forms`
2. Create a form
3. Add your fields and validation rules
4. Save the form
5. Open the public URL `/forms/{slug}`
6. Submit the form and review saved entries from the submissions page

## Routes

Admin and builder routes:

- `/dynamic-forms`
- `/dynamic-forms/create`
- `/dynamic-forms/{form}/edit`
- `/dynamic-forms/{form}/submissions`

Public routes:

- `/forms/{slug}`
- `POST /forms/{slug}`

## Blade Usage

You can render a saved form directly inside any Blade file:

```blade
@dynamicForm('contact-form')
```

The value should be the form slug created in the builder.

## Validation Rules

The package accepts normal Laravel validation rules in the field `Validation` input.

Examples:

- `required`
- `max:255`
- `required|email|max:255`
- `mimes:pdf,jpg,png|max:2048`

Do not wrap rules in backticks.

## File Uploads

Uploaded files are stored using the configured disk:

```php
'storage_disk' => env('DYNAMIC_FORM_STORAGE_DISK', 'public'),
```

Default upload directory:

```php
'upload_directory' => 'dynamic-forms',
```

If you use the `public` disk, make sure `php artisan storage:link` has been run.

## Configuration

The config file is:

```php
config/dynamic-form.php
```

Available options:

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

## Example Workflow

1. Create a form named `Contact Form`
2. Use slug `contact-form`
3. Add fields like `name`, `email`, `message`, and `resume`
4. Add validation like `required|max:255`, `required|email`, `required|min:10`, and `nullable|mimes:pdf,doc,docx|max:2048`
5. Save and open `/forms/contact-form`

## Testing

Run the package test:

```bash
php artisan test --filter=DynamicFormPackageTest
```

## Notes

- Provider auto-discovery is enabled through Composer
- Form submissions are stored in the database
- File metadata is stored with each submission
