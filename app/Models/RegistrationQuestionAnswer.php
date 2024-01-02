<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationQuestionAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['text', 'user_id', 'question_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(RegistrationQuestion::class);
    }
}
