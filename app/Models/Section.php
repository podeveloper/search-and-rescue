<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;
    protected $with = ['module','progressRecords'];
    protected $fillable = [
        'module_id',
        'title',
        'description',
        'sort_number',
    ];


    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function video()
    {
        return $this->hasOne(Video::class);
    }

    public function progressRecords()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function previousSectionId()
    {
        return $this
            ->where('id', '<', $this->id)
            ->where('module_id', '=', $this->module_id)
            ->max('id') ?: null;
    }

    public function nextSectionId()
    {
        return $this
            ->where('id', '>', $this->id)
            ->where('module_id', '=', $this->module_id)
            ->min('id') ?: null;
    }

    public function isFirstSection()
    {
        return $this->previousSectionId() == null;
    }

    public function isLastSection()
    {
        return $this->nextSectionId() == null;
    }

    public function viewable()
    {
        $isPreviousSectionCompleted = \App\Models\UserProgress::query()
            ->where('user_id','=',auth()->user()->id)
            ->where('training_id','=',$this->module->training->id)
            ->where('section_id','=',$this->previousSectionId())
            ->whereNotNull('completed_at')
            ->first();

        $previousModule = Module::find($this->module->previousModuleId());

        $isPreviousModuleCompleted = \App\Models\UserProgress::query()
        ->where('user_id','=',auth()->user()->id)
        ->where('training_id','=',$this->module->training->id)
        ->where('module_id','=',$this->module->previousModuleId())
        ->whereNotNull('completed_at')
        ->pluck('section_id')
        ->contains($previousModule?->lastSectionId());

        $isFirstSection = $this->isFirstSection();
        $isFirstModule = $this->module->isFirstModule();
        return
            $isFirstSection && $isFirstModule ||
            $isPreviousSectionCompleted ||
            ($isPreviousModuleCompleted && $isFirstSection);
    }
}
