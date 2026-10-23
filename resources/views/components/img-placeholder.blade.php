@props(['label' => 'Image placeholder'])

<div {{ $attributes->merge(['class' => 'img-placeholder']) }} role="img" aria-label="{{ $label }}">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-10 w-10" fill="none" stroke="currentColor"
        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <rect x="5" y="9" width="38" height="30" rx="4" />
        <circle cx="16" cy="19" r="3.5" />
        <path d="M5 34l11-10 8 7 7-6 12 10" />
    </svg>
    <span class="mt-2 px-3 text-center text-sm font-bold">{{ $label }}</span>
</div>
