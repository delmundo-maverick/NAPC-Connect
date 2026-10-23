<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · NAPC Connect</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="min-h-screen bg-[var(--mist)]">
        <div class="mx-auto flex max-w-xl flex-col px-5 py-8">

            <a href="{{ route('home') }}"
                class="mb-6 flex items-center gap-2 text-2xl font-extrabold text-[var(--navy)]">
                <span class="inline-block h-6 w-6 rounded-full bg-[var(--sun)]"></span>
                NAPC Connect
            </a>

            <main class="rounded-2xl border-2 border-[var(--navy)] bg-white p-6 sm:p-8">
                @yield('content')
            </main>

            <p class="muted mt-6 text-center">
                <a href="{{ route('home') }}" class="font-semibold underline">Back to home</a>
            </p>
        </div>
    </div>
</body>

</html>
