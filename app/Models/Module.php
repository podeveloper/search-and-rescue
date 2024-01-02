<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    protected $with = ['training'];

    protected $fillable = ['title', 'description', 'training_id','sort_number'];


    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function previousModuleId()
    {
        return $this
            ->where('id', '<', $this->id)
            ->where('training_id', '=', $this->training_id)
            ->max('id') ?: null;
    }

    public function nextModuleId()
    {
        return $this
            ->where('id', '>', $this->id)
            ->where('training_id', '=', $this->training_id)
            ->min('id') ?: null;
    }

    public function isFirstModule()
    {
        return $this->previousModuleId() == null;
    }

    public function isLastModule()
    {
        return $this->nextModuleId() == null;
    }

    public function firstSectionId()
    {
        return $this->sections()->where('module_id', $this->id)->min('id') ?: null;
    }

    public function lastSectionId()
    {
        return $this->sections()->where('module_id', $this->id)->max('id') ?: null;
    }
}
