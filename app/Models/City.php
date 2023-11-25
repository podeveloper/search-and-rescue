<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;

    public $timestamps = false;

    protected $fillable = ['name','code','country_id'];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }
}
