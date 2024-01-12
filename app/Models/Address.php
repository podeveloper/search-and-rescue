<?php

namespace App\Models;

use App\Helpers\DistanceHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    protected $fillable = [
        'type',
        'country_id',
        'city_id',
        'district_id',
        'user_id',
        'full_address',
        'distance_from_center',
        'estimated_time_of_arrival',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            DistanceHelper::updateDistanceAndDuration($model);
        });

        static::updating(function ($model) {
            DistanceHelper::updateDistanceAndDuration($model);
        });
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
