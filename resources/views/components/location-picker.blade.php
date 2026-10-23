{{-- Region, province and city dropdowns. Fields are named region, province and city.
     Pass the selected codes (use old() first, then the saved value). --}}
@props([
    'region' => null,
    'province' => null,
    'city' => null,
    'regionLabel' => 'Region',
    'provinceLabel' => 'Province',
    'cityLabel' => 'City or municipality',
    'regionPlaceholder' => 'Choose a region',
    'provincePlaceholder' => 'Choose a province',
    'cityPlaceholder' => 'Choose a city or municipality',
])

<div x-data="locationPicker" class="space-y-4" data-region="{{ $region }}" data-province="{{ $province }}"
    data-city="{{ $city }}" data-provinces-url="{{ route('locations.provinces') }}"
    data-cities-url="{{ route('locations.cities') }}" data-province-placeholder="{{ $provincePlaceholder }}"
    data-city-placeholder="{{ $cityPlaceholder }}">

    <div class="grid gap-4 sm:grid-cols-2">
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
    </div>

    <div>
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

    <p class="muted" x-show="failed" x-cloak>Could not load places. Check your connection and reload the page.</p>
</div>
