@php
    $type = request()->query('type');
    $links = [
        ['label' => 'My home', 'href' => route('seeker.home'), 'active' => request()->routeIs('seeker.home')],
        [
            'label' => 'Browse',
            'href' => route('seeker.listings.index'),
            'active' => request()->routeIs('seeker.listings.*') && !$type,
        ],
        [
            'label' => 'Ayuda',
            'href' => route('seeker.listings.index', ['type' => 'ayuda']),
            'active' => $type === 'ayuda',
        ],
        [
            'label' => 'Scholarships',
            'href' => route('seeker.listings.index', ['type' => 'scholarship']),
            'active' => $type === 'scholarship',
        ],
        ['label' => 'Jobs', 'href' => route('seeker.listings.index', ['type' => 'job']), 'active' => $type === 'job'],
        [
            'label' => 'Training',
            'href' => route('seeker.listings.index', ['type' => 'training']),
            'active' => $type === 'training',
        ],
        [
            'label' => 'Applications',
            'href' => route('seeker.applications.index'),
            'active' => request()->routeIs('seeker.applications.*'),
        ],
        [
            'label' => 'Profile',
            'href' => route('seeker.profile.edit'),
            'active' => request()->routeIs('seeker.profile.*'),
        ],
    ];
@endphp

<x-navbar.shell :links="$links" />
