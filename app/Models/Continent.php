<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Continent extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['name','name_en'];


    public function countries(): HasMany
    {
        return $this->hasMany(Country::class);
    }
}
