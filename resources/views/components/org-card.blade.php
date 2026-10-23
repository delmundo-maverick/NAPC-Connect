{{-- One organization application with the right buttons for its status --}}
@props(['org'])

@php
    $orgTypes = [
        'school' => 'School or university',
        'lgu' => 'Local government unit (LGU)',
        'agency' => 'Government agency',
        'company' => 'Company or recruiter',
        'ngo' => 'NGO or non-profit',
    ];
    $status = $org->poster_status ?? 'pending';
    $host = parse_url($org->proof_url, PHP_URL_HOST) ?: $org->proof_url;
@endphp

<article class="card-lg border-2 border-[var(--navy)]">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="h3 min-w-0 break-words">{{ $org->organization }}</h3>
        <span class="badge-{{ $status }}">{{ ucfirst($status) }}</span>
    </div>

    <dl class="detail-list mt-5">
        <div><dt>Type</dt><dd>{{ $orgTypes[$org->organization_type] ?? $org->organization_type }}</dd></div>
        <div><dt>Position</dt><dd>{{ $org->position }}</dd></div>
        <div><dt>Applicant</dt><dd>{{ $org->name }}</dd></div>
        <div><dt>Email</dt><dd>{{ $org->email }}</dd></div>
        <div><dt>Registered</dt><dd>{{ $org->created_at->format('M j, Y') }}</dd></div>
        <div>
            <dt>Proof link (opens in a new tab)</dt>
            <dd><a href="{{ $org->proof_url }}" target="_blank" rel="noopener noreferrer" class="text-[var(--sky)] underline">{{ $host }}</a></dd>
        </div>
    </dl>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
        @if ($status !== 'approved')
            <form method="POST" action="{{ route('admin.posters.approve', $org) }}" x-data="confirmForm" @submit="submit($event)">
                @csrf
                <button type="submit" class="btn-primary btn-sm w-full sm:w-auto">
                    {{ $status === 'rejected' ? 'Approve instead' : 'Approve' }}
                </button>
            </form>
        @endif

        @if ($status !== 'rejected')
            <form method="POST" action="{{ route('admin.posters.reject', $org) }}" x-data="confirmForm"
                  data-confirm="{{ $status === 'approved' ? 'Revoke approval for ' . $org->organization . '? They will no longer be able to post.' : 'Reject ' . $org->organization . '?' }}"
                  @submit="submit($event)">
                @csrf
                <button type="submit" class="btn-danger btn-sm w-full sm:w-auto">
                    {{ $status === 'approved' ? 'Revoke approval' : 'Reject' }}
                </button>
            </form>
        @endif
    </div>
</article>
