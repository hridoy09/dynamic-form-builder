<form method="POST" action="{{ route('dynamic-form.public.submit', $form->slug) }}" enctype="multipart/form-data" class="stack">
    @csrf

    @foreach ($form->fields as $field)
        @php
            $oldValue = old($field->name);
            $options = $field->optionsList();
        @endphp

        <div>
            <label for="field_{{ $field->name }}">
                {{ $field->label }}
                @if ($field->is_required)
                    <span style="color: var(--accent);">*</span>
                @endif
            </label>

            @if ($field->type === 'textarea')
                <textarea id="field_{{ $field->name }}" name="{{ $field->name }}" placeholder="{{ $field->placeholder }}">{{ $oldValue }}</textarea>
            @elseif ($field->type === 'select')
                <select id="field_{{ $field->name }}" name="{{ $field->name }}">
                    <option value="">Select an option</option>
                    @foreach ($options as $option)
                        <option value="{{ $option }}" @selected($oldValue === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            @elseif ($field->type === 'radio')
                <div class="stack">
                    @foreach ($options as $option)
                        <label class="inline-check">
                            <input type="radio" name="{{ $field->name }}" value="{{ $option }}" @checked($oldValue === $option)>
                            <span>{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
            @elseif ($field->type === 'checkbox')
                @if ($options !== [])
                    @php
                        $checked = is_array($oldValue) ? $oldValue : [];
                    @endphp
                    <div class="stack">
                        @foreach ($options as $option)
                            <label class="inline-check">
                                <input type="checkbox" name="{{ $field->name }}[]" value="{{ $option }}" @checked(in_array($option, $checked, true))>
                                <span>{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <label class="inline-check">
                        <input id="field_{{ $field->name }}" type="checkbox" name="{{ $field->name }}" value="1" @checked((bool) $oldValue)>
                        <span>{{ $field->placeholder ?: 'Yes' }}</span>
                    </label>
                @endif
            @elseif ($field->type === 'file')
                <input id="field_{{ $field->name }}" type="file" name="{{ $field->name }}">
            @else
                <input
                    id="field_{{ $field->name }}"
                    type="{{ in_array($field->type, ['email', 'number', 'date'], true) ? $field->type : 'text' }}"
                    name="{{ $field->name }}"
                    value="{{ $field->type === 'file' ? '' : $oldValue }}"
                    placeholder="{{ $field->placeholder }}"
                >
            @endif

            @if ($field->help_text)
                <div class="hint">{{ $field->help_text }}</div>
            @endif
        </div>
    @endforeach

    <div class="actions">
        <button class="button" type="submit">{{ $form->submit_label ?: 'Submit' }}</button>
    </div>
</form>
