{{-- The "How to apply" box on a listing page. Needs: $listing, $applied --}}
@props(['listing', 'applied' => false])

<aside class="card-lg space-y-4 border-2 border-[var(--navy)]">
    <h2 class="h3">How to apply</h2>
    <p class="muted font-semibold">{{ $listing->deadlineLabel() }}</p>

    @if ($listing->apply_mode === 'form' && $applied)
        <span class="badge-approved">You already applied</span>
        <a href="{{ route('seeker.applications.index') }}" class="btn-ghost w-full border-2 border-[var(--navy)]">View my
            applications</a>
    @elseif ($listing->isExpired())
        <x-alert type="warning">The deadline for this listing has passed.</x-alert>
    @else
        @if ($listing->apply_mode === 'form')
            <a href="{{ route('seeker.applications.create', $listing) }}" class="btn-accent w-full">Apply now</a>
        @elseif ($listing->apply_mode === 'link')
            <a href="{{ $listing->apply_url }}" target="_blank" rel="noopener noreferrer" class="btn-accent w-full">Open
                the application form</a>
            <p class="muted">This opens the organization’s own form in a new tab.</p>
        @endif

        @if ($listing->how_to_apply)
            <div>
                <h3 class="label">{{ $listing->apply_mode === 'info' ? 'Instructions' : 'Before you apply' }}</h3>
                <p class="text-block">{{ $listing->how_to_apply }}</p>
            </div>
        @endif
    @endif
</aside>
