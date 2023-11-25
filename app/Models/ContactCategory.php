<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactCategory extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;

    public $timestamps = false;

    protected $fillable = ['name'];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
