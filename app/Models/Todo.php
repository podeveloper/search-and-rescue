<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\IsFinished;
use App\Traits\CacheUpdateTrait;
use Filament\Actions\Concerns\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Todo extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;
    use HasLabel;

    protected $table = 'todos';

    protected $fillable = [
        'image',
        'title',
        'content',
        'category_id',
        'is_finished',
        'created_at',
        'deadline_at',
    ];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }

    public function scopeFinished(Builder $query)
    {
        return $query->where('is_finished', '=',1);
    }

    public function scopeUnfinished(Builder $query)
    {
        return $query->where('is_finished', '=',0);
    }

    public function scopeDeadlinePassed(Builder $query)
    {
        return $query->whereDate('deadline_at', '<',now()->toDateString());
    }

    public function scopeInProgress(Builder $query)
    {
        return $query->whereDate('deadline_at', '>=',now()->toDateString());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TodoCategory::class,'category_id');
    }

    public function assignors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assignor_todo','todo_id','assignor_id');
    }

    public function responsibles(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'responsible_todo','todo_id','responsible_id');
    }

    public function scopeAssignedToMe(Builder $query)
    {
        $userId = auth()->id();
        return $query->whereHas('responsibles', function ($q) use ($userId) {
            $q->where('responsible_id', $userId);
        });
    }

    public function scopeAssignedByMe(Builder $query)
    {
        $userId = auth()->id(); // Retrieve the authenticated user's ID
        return $query->whereHas('assignors', function ($q) use ($userId) {
            $q->where('assignor_id', $userId);
        });
    }
}
