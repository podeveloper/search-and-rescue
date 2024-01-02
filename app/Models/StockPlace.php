<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockPlace extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['name','type'];


    public function materialStocks(): HasMany
    {
        return $this->hasMany(MaterialStock::class);
    }
}
