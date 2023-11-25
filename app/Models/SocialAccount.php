<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;


    public $timestamps = false;

    protected $fillable = [ 'user_id', 'platform', 'username'];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
