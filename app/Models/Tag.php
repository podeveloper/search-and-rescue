<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;

    public $timestamps = false;

    protected $fillable = ['name'];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'taggable');
    }

    public function contacts(): MorphToMany
    {
        return $this->morphedByMany(Contact::class, 'taggable');
    }
}
