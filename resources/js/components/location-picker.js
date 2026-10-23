// Region, province and city dropdowns that load from the local PSGC tables.
// Regions are rendered by Blade. Provinces and cities are fetched when needed.
// NCR has no provinces, so its cities load by region.
// Used with: <x-location-picker> (see resources/views/components/location-picker.blade.php)
export default function (Alpine) {
    Alpine.data('locationPicker', () => ({
        region: '',
        province: '',
        city: '',
        provinces: [],
        cities: [],
        loadingProvinces: false,
        loadingCities: false,
        failed: false,
        urls: {},
        labels: {},

        async init() {
            const d = this.$el.dataset;

            this.urls = { provinces: d.provincesUrl, cities: d.citiesUrl };
            this.labels = {
                province: d.provincePlaceholder || 'Choose a province',
                city: d.cityPlaceholder || 'Choose a city or municipality',
            };
            this.region = d.region || '';
            this.province = d.province || '';
            this.city = d.city || '';

            if (this.region) {
                await this.loadProvinces();
            }
        },

        get hasProvinces() {
            return this.provinces.length > 0;
        },

        // A city can be picked once the region is chosen, and the province too if the region has any
        get cityReady() {
            return this.region !== '' && (this.province !== '' || !this.hasProvinces);
        },

        get provinceText() {
            if (!this.region) return 'Pick a region first';
            if (this.loadingProvinces) return 'Loading...';
            return this.hasProvinces ? this.labels.province : 'No provinces here';
        },

        get cityText() {
            if (!this.region) return 'Pick a region first';
            if (this.loadingProvinces || this.loadingCities) return 'Loading...';
            if (!this.cityReady) return 'Pick a province first';
            return this.cities.length === 0 ? 'No cities found' : this.labels.city;
        },

        async load(url, params) {
            const response = await fetch(`${url}?${new URLSearchParams(params)}`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) throw new Error('Request failed');

            return response.json();
        },

        async loadProvinces() {
            const region = this.region;

            this.provinces = [];
            this.failed = false;

            if (!region) {
                await this.loadCities();
                return;
            }

            this.loadingProvinces = true;

            try {
                const items = await this.load(this.urls.provinces, { region });

                if (region !== this.region) return; // the region changed while loading

                this.provinces = items;
            } catch {
                this.failed = true;
            } finally {
                this.loadingProvinces = false;
            }

            await this.loadCities();
        },

        async loadCities() {
            const { region, province } = this;

            this.cities = [];

            if (!this.cityReady) return;

            this.loadingCities = true;

            try {
                const items = await this.load(this.urls.cities, { region, province });

                if (region !== this.region || province !== this.province) return;

                this.cities = items;
            } catch {
                this.failed = true;
            } finally {
                this.loadingCities = false;
            }
        },

        regionChanged() {
            this.province = '';
            this.city = '';

            return this.loadProvinces();
        },

        provinceChanged(value) {
            this.province = value;
            this.city = '';

            return this.loadCities();
        },
    }));
}
