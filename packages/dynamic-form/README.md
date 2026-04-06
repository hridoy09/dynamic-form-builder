# Dynamic Form Package

This local package adds a reusable dynamic form builder to the Laravel app.

## Included features

- Create and edit forms
- Add dynamic fields
- Apply Laravel validation rules per field
- Render forms in Blade using `@dynamicForm('slug')`
- Save submissions
- Upload files
- Browse saved submissions

## Main routes

- `/dynamic-forms` for the builder UI
- `/forms/{slug}` for public form rendering
