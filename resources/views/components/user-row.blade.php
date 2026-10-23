@props(['user'])

<li class="list-row">
    <span class="avatar shrink-0">{{ mb_strtoupper(mb_substr($user->first_name, 0, 1)) }}</span>

    <div class="min-w-0 flex-1 basis-48">
        <p class="truncate font-extrabold text-[var(--navy)]">{{ $user->name }}</p>
        <p class="muted truncate">{{ $user->email }}</p>
        @if ($user->role === 'poster' && $user->organization)
            <p class="muted truncate">{{ $user->organization }}</p>
        @endif
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <span class="{{ $user->role === 'seeker' ? 'badge-muted' : 'badge-role' }}">{{ $user->roleLabel() }}</span>
        @if ($user->role === 'poster' && $user->poster_status)
            <span class="badge-{{ $user->poster_status }}">{{ ucfirst($user->poster_status) }}</span>
        @endif
        <span class="muted">Joined {{ $user->created_at->format('M j, Y') }}</span>
    </div>
</li>
