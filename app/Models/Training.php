<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    protected $fillable = ['title', 'description','training_category_id'];


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'training_user')
            ->where('training_id',$this->id)
            ->withPivot('registered_at', 'finished_at');
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function finished_at_by($userId)
    {
        $finished_at = $this->users()
            ->where('training_id',$this->id)
            ->where('user_id',$userId)
            ->first()?->pivot?->finished_at;

        return ($finished_at) ? Carbon::parse($finished_at)->diffForHumans() : null;
    }

    public function registered_at_by($userId)
    {
        $registered_at = $this->users()
            ->where('training_id',$this->id)
            ->where('user_id',$userId)
            ->first()?->pivot?->registered_at;

        return ($registered_at) ? Carbon::parse($registered_at)->diffForHumans() : null;
    }

    public function sections()
    {
        return $this->hasManyThrough(Section::class, Module::class);
    }

    public function trainingCategory()
    {
        return $this->belongsTo(TrainingCategory::class,'training_category_id');
    }

    public function isCompletedBeforeBy(User $user)
    {
        return ($this->users()->find($user->id)->pivot->finished_at) != null;
    }
}
