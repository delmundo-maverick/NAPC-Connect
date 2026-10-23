<?php

namespace App\Support;

use App\Models\City;
use App\Models\Province;
use App\Models\Region;
use Illuminate\Support\Facades\Cache;

class Locations
{
    private static ?array $map = null;

    /** [code => label] for every region, in code order */
    public static function regions(): array
    {
        return self::map()['regions'];
    }

    public static function region(?string $code): ?string
    {
        return $code ? (self::map()['regions'][$code] ?? null) : null;
    }

    public static function province(?string $code): ?string
    {
        return $code ? (self::map()['provinces'][$code] ?? null) : null;
    }

    public static function city(?string $code): ?string
    {
        return $code ? (self::map()['cities'][$code] ?? null) : null;
    }

    /** Called after a sync */
    public static function forget(): void
    {
        self::$map = null;
        Cache::forget('psgc.names');
    }

    private static function map(): array
    {
        if (self::$map !== null) {
            return self::$map;
        }

        $map = Cache::get('psgc.names');

        if ($map === null) {
            $map = [
                'regions'   => Region::orderBy('code')->get()->mapWithKeys(fn(Region $r) => [$r->code => $r->label()])->all(),
                'provinces' => Province::pluck('name', 'code')->all(),
                'cities'    => City::pluck('name', 'code')->all(),
            ];

            if ($map['regions'] !== []) {
                Cache::forever('psgc.names', $map);
            }
        }

        return self::$map = $map;
    }
}
