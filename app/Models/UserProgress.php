<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'section_id',
        'module_id',
        'training_id',
        'duration',
        'completed_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
