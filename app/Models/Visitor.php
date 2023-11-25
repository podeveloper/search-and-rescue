<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Visitor extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;

    protected $fillable = [
        'name',
        'surname',
        'full_name',
        'gender_id',
        'nationality_id',
        'country_id',
        'language_id',
        'companion_id',
        'phone',
        'email',
        'facebook',
        'twitter',
        'instagram',
        'telegram',
        'occupation_id',
        'occupation_text',
        'organisation_id',
        'organisation_text',
        'explanation',
        'profile_photo',
        'group_number',
    ];

    protected static function boot()
    {
        parent::boot();
        self::bootCacheUpdateTrait();

        static::creating(function ($user) {
            $user->full_name = $user->name . ' ' . $user->surname;
        });

        static::updating(function ($user) {
            if ($user->isDirty(['name', 'surname'])) {
                $user->full_name = $user->name . ' ' . $user->surname;
            }
        });
    }
    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function companion(): BelongsTo
    {
        return $this->belongsTo(User::class,'companion_id');
    }

    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function events(): MorphToMany
    {
        return $this->morphedByMany(Event::class, 'visitorable');
    }

    public function organisationVisits(): MorphToMany
    {
        return $this->morphedByMany(OrganisationVisit::class, 'visitorable');
    }
}
