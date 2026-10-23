@props([
    'region' => null,
    'province' => null,
    'city' => null,
    'columns' => 2,
    'regionLabel' => 'Region',
    'provinceLabel' => 'Province',
    'cityLabel' => 'City or municipality',
    'regionPlaceholder' => 'Choose a region',
    'provincePlaceholder' => 'Choose a province',
    'cityPlaceholder' => 'Choose a city or municipality',
])

<div x-data="locationPicker" class="grid gap-4 {{ $columns === 3 ? 'md:grid-cols-3' : 'sm:grid-cols-2' }}"
    data-region="{{ $region }}" data-province="{{ $province }}" data-city="{{ $city }}"
    data-provinces-url="{{ route('locations.provinces') }}" data-cities-url="{{ route('locations.cities') }}"
    data-province-placeholder="{{ $provincePlaceholder }}" data-city-placeholder="{{ $cityPlaceholder }}">

    <div>
        <label for="region" class="label">{{ $regionLabel }}</label>
        <select id="region" name="region" class="input" x-model="region" @change="regionChanged()">
            <option value="">{{ $regionPlaceholder }}</option>
            @foreach (\App\Support\Locations::regions() as $code => $name)
                <option value="{{ $code }}">{{ $name }}</option>
            @endforeach
        </select>
        @error('region')
            <p class="field-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="province" class="label">{{ $provinceLabel }}</label>
        <select id="province" name="province" class="input" :disabled="!hasProvinces"
            @change="provinceChanged($event.target.value)">
            <option value="" x-text="provinceText"></option>
            <template x-for="item in provinces" :key="item.code">
                <option :value="item.code" :selected="item.code === province" x-text="item.name"></option>
            </template>
        </select>
        @error('province')
            <p class="field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="{{ $columns === 3 ? '' : 'sm:col-span-2' }}">
        <label for="city" class="label">{{ $cityLabel }}</label>
        <select id="city" name="city" class="input" :disabled="!cityReady || cities.length === 0"
            @change="city = $event.target.value">
            <option value="" x-text="cityText"></option>
            <template x-for="item in cities" :key="item.code">
                <option :value="item.code" :selected="item.code === city" x-text="item.name"></option>
            </template>
        </select>
        @error('city')
            <p class="field-error">{{ $message }}</p>
        @enderror
    </div>

    <p class="muted col-span-full" x-show="failed" x-cloak>Could not load places. Check your connection and reload the
        page.</p>
</div>
