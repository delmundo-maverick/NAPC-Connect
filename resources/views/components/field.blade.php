@props(['name', 'label', 'type' => 'text', 'hint' => null, 'value' => null])

<div>
    <label for="{{ $name }}" class="label">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        value="{{ $type === 'password' ? '' : old($name, $value) }}" {{ $attributes->merge(['class' => 'input']) }}>
    @if ($hint)
        <p class="muted mt-1">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm font-semibold text-[var(--coral)]">{{ $message }}</p>
    @enderror
</div>
