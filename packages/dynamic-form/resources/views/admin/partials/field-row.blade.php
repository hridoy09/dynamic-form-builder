@php
    $optionsValue = is_array($field['options'] ?? null)
        ? implode(PHP_EOL, $field['options'])
        : ($field['options'] ?? '');
@endphp

<div class="field-card stack" data-field-row>
    <div class="actions" style="justify-content: space-between;">
        <strong>Field</strong>
        <div class="actions">
            <button type="button" class="button secondary" data-field-action="up">Up</button>
            <button type="button" class="button secondary" data-field-action="down">Down</button>
            <button type="button" class="button secondary" data-field-action="remove">Remove</button>
        </div>
    </div>

    <div class="grid-3">
        <div>
            <label for="fields_{{ $index }}_label">Label</label>
            <input data-field-input id="fields_{{ $index }}_label" name="fields[{{ $index }}][label]" value="{{ $field['label'] ?? '' }}" required>
        </div>
        <div>
            <label for="fields_{{ $index }}_name">Field name</label>
            <input data-field-input id="fields_{{ $index }}_name" name="fields[{{ $index }}][name]" value="{{ $field['name'] ?? '' }}" required>
        </div>
        <div>
            <label for="fields_{{ $index }}_type">Type</label>
            <select data-field-input id="fields_{{ $index }}_type" name="fields[{{ $index }}][type]">
                @foreach ($fieldTypes as $value => $label)
                    <option value="{{ $value }}" @selected(($field['type'] ?? 'text') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid-3">
        <div>
            <label for="fields_{{ $index }}_placeholder">Placeholder</label>
            <input data-field-input id="fields_{{ $index }}_placeholder" name="fields[{{ $index }}][placeholder]" value="{{ $field['placeholder'] ?? '' }}">
        </div>
        <div>
            <label for="fields_{{ $index }}_validation">Validation</label>
            <input data-field-input id="fields_{{ $index }}_validation" name="fields[{{ $index }}][validation]" value="{{ $field['validation'] ?? '' }}">
            <div class="hint">Examples: max:255, min:5|max:5000, mimes:pdf,jpg. Do not include backticks.</div>
        </div>
        <div>
            <label for="fields_{{ $index }}_sort_order">Sort order</label>
            <input data-field-input id="fields_{{ $index }}_sort_order" name="fields[{{ $index }}][sort_order]" type="number" min="1" value="{{ $field['sort_order'] ?? ($index + 1) }}">
        </div>
    </div>

    <div class="grid-2">
        <div>
            <label for="fields_{{ $index }}_help_text">Help text</label>
            <input data-field-input id="fields_{{ $index }}_help_text" name="fields[{{ $index }}][help_text]" value="{{ $field['help_text'] ?? '' }}">
        </div>
        <div>
            <label for="fields_{{ $index }}_options">Options</label>
            <textarea data-field-input id="fields_{{ $index }}_options" name="fields[{{ $index }}][options]">{{ $optionsValue }}</textarea>
            <div class="hint">One option per line. Useful for select, radio, and checkbox fields.</div>
        </div>
    </div>

    <label class="inline-check">
        <input type="hidden" data-field-input name="fields[{{ $index }}][is_required]" value="0">
        <input type="checkbox" data-field-input id="fields_{{ $index }}_is_required" name="fields[{{ $index }}][is_required]" value="1" @checked($field['is_required'] ?? false)>
        <span>Required field</span>
    </label>
</div>

