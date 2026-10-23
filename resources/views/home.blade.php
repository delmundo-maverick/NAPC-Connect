@extends('layouts.app')

@section('title', 'NAPC Connect: ayuda, scholarships, jobs and training in one place')


@section('navbar')
    @auth
        <x-dynamic-component :component="'navbar.' . auth()->user()->role" />
    @else
        <x-navbar.guest />
    @endauth
@endsection
@section('content')
    {{-- HERO --}}
    <section class="section-mist">
        <div class="container-page grid items-center gap-12 py-14 lg:grid-cols-2 lg:py-20">

            <div class="rise">
                <h1 class="h1">Ayuda, scholarships, jobs and training. Stop searching page by page.</h1>
                <p class="lead mt-5">
                    Programs from DSWD, CHED, TESDA, schools and your LGU are posted all over Facebook and agency sites.
                    NAPC Connect puts the verified ones in one place and shows you the ones that fit you.
                </p>

                {{-- SEARCH --}}
                <form action="/listings" method="GET" class="mt-8" x-data="searchForm">
                    <input type="hidden" name="type" :value="type">

                    <div class="flex flex-wrap gap-2" role="group" aria-label="What are you looking for?">
                        @foreach (['' => 'All', 'ayuda' => 'Ayuda', 'scholarship' => 'Scholarships', 'job' => 'Jobs', 'training' => 'Training'] as $val => $label)
                            <button type="button" @click="setType('{{ $val }}')"
                                :class="chipClass('{{ $val }}')">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                        <label for="q" class="sr-only">Search programs</label>
                        <input id="q" name="q" type="search" autocomplete="off" class="input"
                            placeholder="Try “TESDA welding” or “scholarship for BSIT”">
                        <button type="submit" class="btn-accent shrink-0 px-7">Search</button>
                    </div>

                    <p class="muted mt-4 font-semibold">Popular searches</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach (['4Ps ayuda', 'Senior citizen pension', 'CHED scholarship', 'TESDA NC II', 'Work from home'] as $chip)
                            <a href="/listings?q={{ urlencode($chip) }}" class="chip-soft">{{ $chip }}</a>
                        @endforeach
                    </div>
                </form>
            </div>

            {{-- Hero image with one sample match --}}
            <div class="rise rise-2 relative pb-10 sm:pb-12">
                <img src="{{ asset('images/hero-img.jpg') }}" alt="..."
                    class="aspect-[4/3] w-full rounded-2xl object-cover">
                class="aspect-[4/3] w-full" />

                <article class="card absolute -bottom-0 left-4 right-4 border-2 border-[var(--navy)] sm:left-8 sm:right-8">
                    <div class="flex items-center justify-between gap-3">
                        <span class="badge-scholarship">Scholarship</span>
                        <span class="deadline-soon">Closes in 9 days</span>
                    </div>
                    <h3 class="mt-2 text-lg font-extrabold text-[var(--navy)]">SUC Tertiary Education Subsidy</h3>
                    <p class="muted">CHED · Batangas</p>
                    <p class="match-reason mt-1">Matches your year level and school type</p>
                </article>
            </div>
        </div>
    </section>

    {{-- CATEGORIES --}}
    <section class="section">
        <h2 class="h2 max-w-2xl">Four kinds of help, one search</h2>
        <p class="lead mt-3">Pick a category, or search across all of them.</p>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $cats = [
                    [
                        'ayuda',
                        'Ayuda',
                        'Cash aid, relief goods, pensions and other programs from government and LGUs.',
                        'SDG 1, No Poverty',
                    ],
                    [
                        'scholarship',
                        'Scholarships',
                        'Filter by your year level, school and location to see what you can apply for.',
                        'SDG 4, Quality Education',
                    ],
                    [
                        'job',
                        'Jobs',
                        'Openings from local employers and recruiters, with salary and setup shown upfront.',
                        'SDG 8, Decent Work',
                    ],
                    [
                        'training',
                        'Training',
                        'TESDA and other skills programs, including free and allowance-supported courses.',
                        'SDG 8, Decent Work',
                    ],
                ];
            @endphp

            @foreach ($cats as [$slug, $name, $desc, $sdg])
                <a href="/listings?type={{ $slug }}" class="cat-card cat-{{ $slug }}">
                    {{-- Replace with an <img> for each category --}}
                    <x-img-placeholder label="{{ $name }} photo"
                        class="img-placeholder-on-color mb-5 aspect-[16/10] w-full" />
                    <div>
                        <h3 class="text-2xl font-extrabold">{{ $name }}</h3>
                        <p class="mt-2 leading-snug">{{ $desc }}</p>
                    </div>
                    <p class="mt-5 text-sm font-bold">Supports {{ $sdg }}</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- SAMPLE RECOMMENDATIONS --}}
    <section class="section-mist">
        <div class="section">
            <h2 class="h2 max-w-2xl">Suggestions based on your profile</h2>
            <p class="lead mt-3">Example for a 2nd year BSIT student in Lipa City. These are samples, not real listings.</p>

            <div class="mt-10 grid gap-4 md:grid-cols-3">
                <article class="card-lg">
                    <div class="flex items-center justify-between gap-3">
                        <span class="badge-scholarship">Scholarship</span>
                        <span class="deadline-soon">Closes in 9 days</span>
                    </div>
                    <h3 class="h3 mt-3">SUC Tertiary Education Subsidy</h3>
                    <p class="muted">CHED · Batangas</p>
                    <p class="match-reason mt-3">Matches your year level and school type</p>
                </article>

                <article class="card-lg">
                    <div class="flex items-center justify-between gap-3">
                        <span class="badge-training">Training</span>
                        <span class="muted font-bold">Open enrollment</span>
                    </div>
                    <h3 class="h3 mt-3">Web Development NC III</h3>
                    <p class="muted">TESDA · Lipa City</p>
                    <p class="match-reason mt-3">Matches your location and course</p>
                </article>

                <article class="card-lg">
                    <div class="flex items-center justify-between gap-3">
                        <span class="badge-ayuda">Ayuda</span>
                        <span class="muted font-bold">Ongoing</span>
                    </div>
                    <h3 class="h3 mt-3">Student Assistance Program</h3>
                    <p class="muted">City Social Welfare Office</p>
                    <p class="match-reason mt-3">Matches your location</p>
                </article>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS (a real sequence, so numbered) --}}
    <section class="section">
        <h2 class="h2">How NAPC Connect works</h2>

        <ol class="mt-10 grid gap-6 md:grid-cols-3">
            <li class="card-lg bg-[var(--mist)]">
                <span class="step-dot">1</span>
                <h3 class="h3 mt-4">Tell us a little about you</h3>
                <p class="lead mt-2 text-base">Add your year level, school and location. You can also browse without an
                    account.</p>
            </li>
            <li class="card-lg bg-[var(--mist)]">
                <span class="step-dot">2</span>
                <h3 class="h3 mt-4">See what fits</h3>
                <p class="lead mt-2 text-base">Get suggested programs with a short reason, and narrow them with search and
                    filters.</p>
            </li>
            <li class="card-lg bg-[var(--mist)]">
                <span class="step-dot">3</span>
                <h3 class="h3 mt-4">Apply at the source</h3>
                <p class="lead mt-2 text-base">Every listing links to the official page or office, so you apply directly.
                </p>
            </li>
        </ol>
    </section>

    {{-- TRUST --}}
    <section class="section-navy">
        <div class="container-page grid items-center gap-8 py-16 md:grid-cols-2">
            <div>
                <h2 class="h2 !text-white">Fake ayuda posts spread fast. We check who posts.</h2>
                <p class="lead mt-4 !text-white/80">
                    Organizations are reviewed before they can publish. Listings show who posted them and link to the
                    original announcement.
                </p>
            </div>
            <div class="card-lg">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="badge-verified">Verified poster</span>
                    <span class="muted font-semibold">City Social Welfare Office</span>
                </div>
                <p class="h3 mt-3">Where this label appears</p>
                <p class="lead mt-1 text-base">On every card and detail page, next to the organization’s name and a link to
                    its official source.</p>
            </div>
        </div>
    </section>

    @guest
        {{-- POSTER CTA --}}
        <section class="section">
            <div class="grid items-center gap-8 rounded-2xl bg-[var(--sun)] p-8 md:grid-cols-[1fr_280px] md:p-10">
                <div>
                    <h2 class="text-3xl font-extrabold text-[var(--navy)]">Run a school, LGU, agency or company?</h2>
                    <p class="mt-2 max-w-xl text-lg text-[var(--navy)]/85">Post your scholarship, ayuda, job or training and
                        add only the details your applicants need.</p>
                    <a href="/register?role=poster" class="btn-primary mt-6">Register as a poster</a>
                </div>
                <x-img-placeholder label="Photo: school or LGU staff" class="img-placeholder-on-color aspect-[4/3] w-full" />
            </div>
        </section>
    @endguest
@endsection
