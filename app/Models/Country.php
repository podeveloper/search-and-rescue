<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'name_en',
        'iso3',
        'iso2',
        'numeric_code',
        'phone_code',
        'capital',
        'currency',
        'currency_name',
        'currency_symbol',
        'tld',
        'native',
        'latitude',
        'longitude',
        'emoji',
        'emojiU',
        'region_id',
        'nationality_id',
        'continent_id',
    ];


    public function continent(): BelongsTo
    {
        return $this->belongsTo(Continent::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
