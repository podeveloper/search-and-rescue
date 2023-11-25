<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationQuestion extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;

    public $timestamps = false;
    protected $fillable = ['text', 'sort_order', 'is_published'];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }
}
