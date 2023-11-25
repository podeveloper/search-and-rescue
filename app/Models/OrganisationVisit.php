<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class OrganisationVisit extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;

    protected $fillable = [
        'date',
        'place_id',
        'organisation_id',
        'host_id',
        'explanation',
    ];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function visitors(): MorphToMany
    {
        return $this->morphToMany(Visitor::class, 'visitorable');
    }
}
