@php
    // Listing pages are only open to approved organizations, so only show them then
    $approved = auth()->user()->poster_status === 'approved';

    $links = [['label' => 'Dashboard', 'href' => route('poster.home'), 'active' => request()->routeIs('poster.home')]];

    if ($approved) {
        $links[] = [
            'label' => 'My listings',
            'href' => route('poster.listings.index'),
            'active' => request()->routeIs(
                'poster.listings.index',
                'poster.listings.edit',
                'poster.listings.applications',
            ),
        ];
        $links[] = [
            'label' => 'New listing',
            'href' => route('poster.listings.create'),
            'active' => request()->routeIs('poster.listings.create'),
        ];
    }
@endphp

<x-navbar.shell :links="$links" />
