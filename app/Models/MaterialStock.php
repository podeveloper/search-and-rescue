<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialStock extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;

    public $timestamps = false;

    protected $fillable = [
        'material_id',
        'stock_place_id',
        'lower_limit',
        'current_amount',
    ];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function stockPlace(): BelongsTo
    {
        return $this->belongsTo(StockPlace::class);
    }
}
