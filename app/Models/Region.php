<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'psgc_regions';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    /** "Region IV-A (CALABARZON)" style label */
    public function label(): string
    {
        if ($this->short_name && ! str_contains($this->name, $this->short_name)) {
            return "{$this->short_name} ({$this->name})";
        }

        return $this->name;
    }
}
