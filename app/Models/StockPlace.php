<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockPlace extends Model
{
    use SoftDeletes, CacheUpdateTrait;

    public $timestamps = false;

    protected $fillable = ['name','type'];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }

    public function materialStocks(): HasMany
    {
        return $this->hasMany(MaterialStock::class);
    }
}
