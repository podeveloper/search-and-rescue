<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'material_id',
        'from_where',
        'to_where',
        'user_id',
        'amount',
    ];


    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function fromWhere(): BelongsTo
    {
        return $this->belongsTo(StockPlace::class,'from_where');
    }

    public function toWhere(): BelongsTo
    {
        return $this->belongsTo(StockPlace::class,'to_where');
    }
}
