@props(['name', 'label', 'rows' => 4, 'hint' => null, 'value' => null])

<div>
    <label for="{{ $name }}" class="label">{{ $label }}</label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'input']) }}>{{ old($name, $value) }}</textarea>
    @if ($hint)
        <p class="muted mt-1">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm font-semibold text-[var(--coral)]">{{ $message }}</p>
    @enderror
</div>
