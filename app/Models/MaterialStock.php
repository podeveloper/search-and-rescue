<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialStock extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'material_id',
        'stock_place_id',
        'lower_limit',
        'current_amount',
    ];

        public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function stockPlace(): BelongsTo
    {
        return $this->belongsTo(StockPlace::class);
    }
}
