<div class="field-card stack" data-repeater-row>
    <div class="field-toolbar">
        <div class="field-meta">
            <span class="field-number" data-repeater-number>{{ is_numeric($index) ? $index + 1 : '__INDEX__' }}</span>
            <div>
                <strong>Automation action</strong>
                <div class="hint">Send structured payloads to CRMs, internal services, or partner systems.</div>
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
            <label for="automation_actions_{{ $index }}_label">Action label</label>
            <input data-repeater-input id="automation_actions_{{ $index }}_label" name="automation_actions[{{ $index }}][label]" value="{{ $action['label'] ?? '' }}">
        </div>
        <div>
            <label for="automation_actions_{{ $index }}_trigger">Trigger</label>
            <select data-repeater-input id="automation_actions_{{ $index }}_trigger" name="automation_actions[{{ $index }}][trigger]">
                @foreach ($workflowTriggers as $trigger)
                    <option value="{{ $trigger }}" @selected(($action['trigger'] ?? 'completed') === $trigger)>{{ ucwords(str_replace('_', ' ', $trigger)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="automation_actions_{{ $index }}_type">Action type</label>
            <select data-repeater-input id="automation_actions_{{ $index }}_type" name="automation_actions[{{ $index }}][type]">
                @foreach ($automationTypes as $value => $label)
                    <option value="{{ $value }}" @selected(($action['type'] ?? 'webhook') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid-2">
        <div>
            <label for="automation_actions_{{ $index }}_endpoint">Endpoint URL</label>
            <input data-repeater-input id="automation_actions_{{ $index }}_endpoint" name="automation_actions[{{ $index }}][endpoint]" value="{{ $action['endpoint'] ?? '' }}" placeholder="https://example.com/hooks/forms">
        </div>
        <div>
            <label for="automation_actions_{{ $index }}_method">HTTP method</label>
            <select data-repeater-input id="automation_actions_{{ $index }}_method" name="automation_actions[{{ $index }}][method]">
                @foreach ($automationMethods as $method)
                    <option value="{{ $method }}" @selected(($action['method'] ?? 'POST') === $method)>{{ $method }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid-2">
        <div>
            <label for="automation_actions_{{ $index }}_headers">Headers</label>
            <textarea data-repeater-input id="automation_actions_{{ $index }}_headers" name="automation_actions[{{ $index }}][headers]">{{ $action['headers'] ?? '' }}</textarea>
            <div class="hint">One header per line. Example: <code>Authorization: Bearer token</code></div>
        </div>
        <div>
            <label for="automation_actions_{{ $index }}_payload">JSON payload template</label>
            <textarea data-repeater-input id="automation_actions_{{ $index }}_payload" name="automation_actions[{{ $index }}][payload]">{{ $action['payload'] ?? '' }}</textarea>
        </div>
    </div>

    <label class="inline-check">
        <input type="hidden" data-repeater-input name="automation_actions[{{ $index }}][is_active]" value="0">
        <input type="checkbox" data-repeater-input id="automation_actions_{{ $index }}_is_active" name="automation_actions[{{ $index }}][is_active]" value="1" @checked($action['is_active'] ?? false)>
        <span>Automation is active</span>
    </label>
</div>
