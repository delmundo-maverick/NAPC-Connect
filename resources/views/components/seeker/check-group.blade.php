@props(['name', 'label', 'options', 'selected' => [], 'hint' => null])

@php
    // After a failed submit use what was ticked, otherwise what is saved
    $selected = (array) ($errors->any() ? old($name, []) : $selected);
@endphp

<fieldset>
    <legend class="label">{{ $label }}</legend>
    <div class="check-grid">
        @foreach ($options as $value => $text)
            <label class="check-item">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $value }}"
                    @checked(in_array($value, $selected, true))>
                <span>{{ $text }}</span>
            </label>
        @endforeach
    </div>
    @if ($hint)
        <p class="muted mt-2">{{ $hint }}</p>
    @endif
</fieldset>
