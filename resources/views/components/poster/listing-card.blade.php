{{-- One listing with its actions. Use "compact" on the dashboard to hide the buttons. --}}
@props(['listing', 'compact' => false])

@php
    $takenDown = $listing->isTakenDown();
    $statusBadge =
        ['draft' => 'badge-muted', 'published' => 'badge-approved', 'closed' => 'badge-role'][$listing->status] ??
        'badge-muted';
    $applyText = match ($listing->apply_mode) {
        'form' => 'NAPC Connect form',
        'link' => 'Link to your own form',
        default => 'No online application',
    };
    $count = $listing->applications_count ?? 0;
@endphp

<article class="card-lg border-2 border-[var(--navy)]">
    <div class="flex flex-wrap items-center gap-2">
        <span class="badge-{{ $listing->type }}">{{ $listing->typeLabel() }}</span>
        @if ($takenDown)
            <span class="badge-removed">Taken down</span>
        @else
            <span class="{{ $statusBadge }}">{{ ucfirst($listing->status) }}</span>
        @endif
    </div>

    <h3 class="h3 mt-3 break-words">
        <a href="{{ route('poster.listings.edit', $listing) }}" class="hover:underline">{{ $listing->title }}</a>
    </h3>

    @if ($takenDown)
        <x-alert type="danger">
            <p class="font-extrabold">An admin took this listing down.</p>
            <p class="mt-1 text-block">{{ $listing->takedown_reason }}</p>
            <p class="mt-1">You can still edit it. It stays hidden until the admin restores it.</p>
        </x-alert>
    @endif

    <dl class="detail-list mt-4">
        <div>
            <dt>Available in</dt>
            <dd>{{ $listing->locationLabel() }}</dd>
        </div>
        <div>
            <dt>Deadline</dt>
            <dd>{{ $listing->deadlineLabel() }}</dd>
        </div>
        <div>
            <dt>How to apply</dt>
            <dd>{{ $applyText }}</dd>
        </div>
        @if ($listing->apply_mode === 'form')
            <div>
                <dt>Applications</dt>
                <dd>{{ $count }}</dd>
            </div>
        @endif
    </dl>

    @unless ($compact)
        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <a href="{{ route('poster.listings.edit', $listing) }}" class="btn-primary btn-sm">Edit</a>

            @if ($listing->apply_mode === 'form')
                <a href="{{ route('poster.listings.applications', $listing) }}"
                    class="btn-ghost btn-sm border-2 border-[var(--navy)]">Applicants ({{ $count }})</a>
            @endif

            @if ($listing->status === 'published')
                <form method="POST" action="{{ route('poster.listings.status', $listing) }}" x-data="confirmForm"
                    data-confirm="Close “{{ $listing->title }}”? Seekers will no longer see it." @submit="submit($event)">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="closed">
                    <button type="submit"
                        class="btn-ghost btn-sm w-full border-2 border-[var(--navy)] sm:w-auto">Close</button>
                </form>
            @elseif (!$takenDown)
                <form method="POST" action="{{ route('poster.listings.status', $listing) }}" x-data="confirmForm"
                    @submit="submit($event)">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="published">
                    <button type="submit"
                        class="btn-accent btn-sm w-full sm:w-auto">{{ $listing->status === 'closed' ? 'Reopen' : 'Publish' }}</button>
                </form>
            @endif

            <form method="POST" action="{{ route('poster.listings.destroy', $listing) }}" x-data="confirmForm"
                data-confirm="Delete “{{ $listing->title }}”? This also removes {{ $count }} application(s) and cannot be undone."
                @submit="submit($event)">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger btn-sm w-full sm:w-auto">Delete</button>
            </form>
        </div>
    @endunless
</article>
