<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Cache;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'date',
        'starts_at',
        'ends_at',
        'location',
        'capacity',
        'organizer',
        'is_published',
        'event_category_id',
        'event_place_id',
        'google_calendar_event_id',
    ];


    public function scopeToday(Builder $query)
    {
        return $query->whereBetween('date', [Carbon::today()->startOfDay(),Carbon::today()->endOfDay()]);
    }

    public function scopeTomorrow(Builder $query)
    {
        return $query->whereBetween('date', [Carbon::tomorrow()->startOfDay(),Carbon::tomorrow()->endOfDay()]);
    }

    public function scopeThisWeek(Builder $query)
    {
        return $query->whereBetween('date', [Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user');
    }

    public function responsibles(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'responsible_event','event_id','responsible_id');
    }

    public function eventCategory(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class);
    }

    public function eventPlace(): BelongsTo
    {
        return $this->belongsTo(EventPlace::class);
    }

    public function scopePublished(Builder $query)
    {
        return $query->where('is_published', '=',1);
    }

    public function scopeNotPublished(Builder $query)
    {
        return $query->where('is_published', '=',0);
    }
}
