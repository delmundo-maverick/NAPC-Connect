@php
    $links = [
        ['label' => 'Dashboard', 'href' => route('admin.home'), 'active' => request()->routeIs('admin.home')],
        [
            'label' => 'Organizations',
            'href' => route('admin.organizations.index'),
            'active' => request()->routeIs('admin.organizations.*'),
        ],
        ['label' => 'Users', 'href' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
        [
            'label' => 'Listings',
            'href' => route('admin.listings.index'),
            'active' => request()->routeIs('admin.listings.*'),
        ],
    ];
@endphp

<x-navbar.shell :links="$links" />
