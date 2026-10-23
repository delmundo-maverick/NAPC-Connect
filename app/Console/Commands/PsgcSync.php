<?php

namespace App\Console\Commands;

use App\Support\Locations;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PsgcSync extends Command
{
    protected $signature = 'psgc:sync
        {--base=https://psgc.gitlab.io/api : API base URL}
        {--insecure : Skip SSL certificate checks (for Windows setups without a CA bundle)}';

    protected $description = 'Copy regions, provinces and cities/municipalities from the PSGC API into the local tables';

    public function handle(): int
    {
        try {
            $this->line('Downloading regions...');
            $regions = $this->fetch('regions');

            $this->line('Downloading provinces...');
            $provinces = $this->fetch('provinces');

            $this->line('Downloading cities and municipalities...');
            $cities = $this->fetch('cities-municipalities');
        } catch (\Throwable $e) {
            $this->error('Could not download the data: ' . $e->getMessage());

            if (str_contains($e->getMessage(), 'cURL error 60')) {
                $this->warn('Your PHP has no SSL certificate bundle. Run again with --insecure, or set curl.cainfo in php.ini.');
            }

            return self::FAILURE;
        }

        $regionRows = collect($regions)
            ->filter(fn($r) => ! empty($r['code']) && ! empty($r['name']))
            ->map(fn($r) => [
                'code'       => (string) $r['code'],
                'name'       => (string) $r['name'],
                'short_name' => ($r['regionName'] ?? '') !== '' ? (string) $r['regionName'] : null,
            ])->values();

        $provinceRows = collect($provinces)
            ->filter(fn($p) => ! empty($p['code']) && ! empty($p['name']) && ! empty($p['regionCode']))
            ->map(fn($p) => [
                'code'        => (string) $p['code'],
                'region_code' => (string) $p['regionCode'],
                'name'        => (string) $p['name'],
            ])->values();

        $cityRows = collect($cities)
            ->filter(fn($c) => ! empty($c['code']) && ! empty($c['name']) && ! empty($c['regionCode']))
            ->map(fn($c) => [
                'code'          => (string) $c['code'],
                'region_code'   => (string) $c['regionCode'],
                // NCR cities and independent cities come with "false" instead of a province code
                'province_code' => is_string($c['provinceCode'] ?? false) && $c['provinceCode'] !== '' ? $c['provinceCode'] : null,
                'name'          => (string) $c['name'],
                'is_city'       => (bool) ($c['isCity'] ?? false),
            ])->values();

        // Stop before touching anything if the data looks wrong
        if ($regionRows->count() < 10 || $provinceRows->count() < 50 || $cityRows->count() < 1000) {
            $this->error("The data looks incomplete ({$regionRows->count()} regions, {$provinceRows->count()} provinces, {$cityRows->count()} cities). Nothing was changed.");

            return self::FAILURE;
        }

        DB::transaction(function () use ($regionRows, $provinceRows, $cityRows) {
            $this->upsert('psgc_regions', $regionRows, ['name', 'short_name']);
            $this->upsert('psgc_provinces', $provinceRows, ['region_code', 'name']);
            $this->upsert('psgc_cities', $cityRows, ['region_code', 'province_code', 'name', 'is_city']);
        });

        Locations::forget();

        $this->info("Done: {$regionRows->count()} regions, {$provinceRows->count()} provinces, {$cityRows->count()} cities and municipalities.");

        return self::SUCCESS;
    }

    private function fetch(string $file): array
    {
        $data = Http::timeout(60)
            ->retry(3, 1000)
            ->acceptJson()
            ->when($this->option('insecure'), fn($http) => $http->withoutVerifying())
            ->get(rtrim($this->option('base'), '/') . "/{$file}.json")
            ->throw()
            ->json();

        if (! is_array($data)) {
            throw new \RuntimeException("{$file}.json did not return a list.");
        }

        return $data;
    }

    private function upsert(string $table, $rows, array $update): void
    {
        $rows->chunk(500)->each(
            fn($chunk) => DB::table($table)->upsert($chunk->values()->all(), ['code'], $update)
        );
    }
}
