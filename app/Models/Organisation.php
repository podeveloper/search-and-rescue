<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;

    protected $fillable = [
        'name',
        'description',
        'industry',
        'phone',
        'email',
        'address',
        'website',
        'country_id',
        'city_id',
    ];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function organisationVisits(): HasMany
    {
        return $this->hasMany(OrganisationVisit::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
