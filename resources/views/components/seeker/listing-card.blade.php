{{-- One listing in the browse grid. On the seeker home it also shows why it matches. --}}
@props(['listing'])

<article class="card-lg flex h-full flex-col border-2 border-[var(--navy)]">
    <span class="badge-{{ $listing->type }} self-start">{{ $listing->typeLabel() }}</span>

    <h3 class="h3 mt-3 break-words">
        <a href="{{ route('seeker.listings.show', $listing) }}" class="hover:underline">{{ $listing->title }}</a>
    </h3>

    @if ($listing->user?->organization)
        <p class="muted mt-1 font-semibold">{{ $listing->user->organization }}</p>
    @endif

    <p class="mt-3 text-sm">{{ \Illuminate\Support\Str::limit(strip_tags($listing->description), 140) }}</p>

    @if (!empty($listing->match_reasons))
        <div class="mt-4 rounded-xl bg-[var(--mist)] p-4">
            <p class="label !mb-1">Why this matches you</p>
            <ul class="list-disc space-y-1 pl-5 text-sm font-semibold">
                @foreach (array_slice($listing->match_reasons, 0, 3) as $reason)
                    <li>{{ $reason }}</li>
                @endforeach
            </ul>
        </div>
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
        @if ($listing->type === 'job' && !empty($listing->details['salary']))
            <div>
                <dt>Salary</dt>
                <dd>{{ $listing->details['salary'] }}</dd>
            </div>
        @endif
    </dl>

    <div class="mt-auto pt-6">
        <a href="{{ route('seeker.listings.show', $listing) }}" class="btn-primary btn-sm w-full sm:w-auto">View
            details</a>
    </div>
</article>
