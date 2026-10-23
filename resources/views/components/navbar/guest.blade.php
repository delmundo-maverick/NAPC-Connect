@php
    $type = request()->query('type');
    $links = [
        ['label' => 'Ayuda', 'href' => '/listings?type=ayuda', 'active' => $type === 'ayuda'],
        ['label' => 'Scholarships', 'href' => '/listings?type=scholarship', 'active' => $type === 'scholarship'],
        ['label' => 'Jobs', 'href' => '/listings?type=job', 'active' => $type === 'job'],
        ['label' => 'Training', 'href' => '/listings?type=training', 'active' => $type === 'training'],
    ];
@endphp

<x-navbar.shell :links="$links" />
