<div class="field-card stack" data-repeater-row>
    <div class="field-toolbar">
        <div class="field-meta">
            <span class="field-number" data-repeater-number>{{ is_numeric($index) ? $index + 1 : '__INDEX__' }}</span>
            <div>
                <strong>Notification rule</strong>
                <div class="hint">Trigger email updates when submissions are received or advanced.</div>
            </div>
        </div>
        <div class="actions">
            <button type="button" class="button tiny secondary" data-repeater-action="up">Up</button>
            <button type="button" class="button tiny secondary" data-repeater-action="down">Down</button>
            <button type="button" class="button tiny flat" data-repeater-action="remove">Remove</button>
        </div>
    </div>

    <div class="grid-3">
        <div>
            <label for="notification_rules_{{ $index }}_label">Rule label</label>
            <input data-repeater-input id="notification_rules_{{ $index }}_label" name="notification_rules[{{ $index }}][label]" value="{{ $rule['label'] ?? '' }}">
        </div>
        <div>
            <label for="notification_rules_{{ $index }}_trigger">Trigger</label>
            <select data-repeater-input id="notification_rules_{{ $index }}_trigger" name="notification_rules[{{ $index }}][trigger]">
                @foreach ($workflowTriggers as $trigger)
                    <option value="{{ $trigger }}" @selected(($rule['trigger'] ?? 'submitted') === $trigger)>{{ ucwords(str_replace('_', ' ', $trigger)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="notification_rules_{{ $index }}_recipient">Recipients</label>
            <input data-repeater-input id="notification_rules_{{ $index }}_recipient" name="notification_rules[{{ $index }}][recipient]" value="{{ $rule['recipient'] ?? '' }}" placeholder="ops@example.com, manager@example.com">
        </div>
    </div>

    <div class="grid-2">
        <div>
            <label for="notification_rules_{{ $index }}_subject">Subject</label>
            <input data-repeater-input id="notification_rules_{{ $index }}_subject" name="notification_rules[{{ $index }}][subject]" value="{{ $rule['subject'] ?? '' }}">
            <div class="hint">
                Tokens:
                @verbatim
                    {{ form.name }}, {{ submission.reference }}, {{ submission.status }}
                @endverbatim
            </div>
        </div>
        <div>
            <label for="notification_rules_{{ $index }}_message">Message</label>
            <textarea data-repeater-input id="notification_rules_{{ $index }}_message" name="notification_rules[{{ $index }}][message]">{{ $rule['message'] ?? '' }}</textarea>
        </div>
    </div>

    <label class="inline-check">
        <input type="hidden" data-repeater-input name="notification_rules[{{ $index }}][is_active]" value="0">
        <input type="checkbox" data-repeater-input id="notification_rules_{{ $index }}_is_active" name="notification_rules[{{ $index }}][is_active]" value="1" @checked($rule['is_active'] ?? false)>
        <span>Rule is active</span>
    </label>
</div>
