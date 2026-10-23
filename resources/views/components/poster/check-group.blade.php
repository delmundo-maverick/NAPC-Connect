@props(['name', 'label', 'options', 'selected' => [], 'hint' => null])

@php
    // After a failed submit, show what the person ticked. Otherwise show what is saved.
    $current = array_map('strval', (array) (session()->hasOldInput() ? old($name, []) : $selected));
@endphp

<fieldset>
    <legend class="label">{{ $label }}</legend>
    @if ($hint)
        <p class="muted mb-2">{{ $hint }}</p>
    @endif

    <div class="grid gap-2 sm:grid-cols-2">
        @foreach ($options as $value => $text)
            <label class="check-pill">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $value }}" class="check-input"
                    @checked(in_array((string) $value, $current, true))>
                <span>{{ $text }}</span>
            </label>
        @endforeach
    </div>

    @error($name)
        <p class="mt-1 text-sm font-semibold text-[var(--coral)]">{{ $message }}</p>
    @enderror
</fieldset>
