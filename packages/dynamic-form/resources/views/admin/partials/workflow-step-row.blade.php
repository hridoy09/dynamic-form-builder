<div class="field-card stack" data-repeater-row>
    <div class="field-toolbar">
        <div class="field-meta">
            <span class="field-number" data-repeater-number>{{ is_numeric($index) ? $index + 1 : '__INDEX__' }}</span>
            <div>
                <strong>Workflow stage</strong>
                <div class="hint">Define who handles this stage and how quickly it should move.</div>
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
            <label for="workflow_steps_{{ $index }}_name">Stage name</label>
            <input data-repeater-input id="workflow_steps_{{ $index }}_name" name="workflow_steps[{{ $index }}][name]" value="{{ $step['name'] ?? '' }}">
        </div>
        <div>
            <label for="workflow_steps_{{ $index }}_type">Stage type</label>
            <select data-repeater-input id="workflow_steps_{{ $index }}_type" name="workflow_steps[{{ $index }}][type]">
                @foreach ($workflowStepTypes as $value => $label)
                    <option value="{{ $value }}" @selected(($step['type'] ?? 'approval') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="workflow_steps_{{ $index }}_assignee">Owner</label>
            <input data-repeater-input id="workflow_steps_{{ $index }}_assignee" name="workflow_steps[{{ $index }}][assignee]" value="{{ $step['assignee'] ?? '' }}" placeholder="Operations, Finance, Team Lead">
        </div>
    </div>

    <div class="grid-2">
        <div>
            <label for="workflow_steps_{{ $index }}_instructions">Instructions</label>
            <textarea data-repeater-input id="workflow_steps_{{ $index }}_instructions" name="workflow_steps[{{ $index }}][instructions]">{{ $step['instructions'] ?? '' }}</textarea>
        </div>
        <div>
            <label for="workflow_steps_{{ $index }}_sla_hours">SLA target in hours</label>
            <input data-repeater-input id="workflow_steps_{{ $index }}_sla_hours" name="workflow_steps[{{ $index }}][sla_hours]" type="number" min="1" max="720" value="{{ $step['sla_hours'] ?? '' }}">
            <div class="hint">Used for context in the admin timeline and handoff notes.</div>
        </div>
    </div>
</div>
