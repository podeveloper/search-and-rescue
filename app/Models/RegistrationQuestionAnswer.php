<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CacheUpdateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationQuestionAnswer extends Model
{
    use HasFactory, SoftDeletes, CacheUpdateTrait;

    protected $fillable = ['text', 'user_id', 'question_id'];

    protected static function boot() {
        parent::boot();
        self::bootCacheUpdateTrait();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(RegistrationQuestion::class);
    }
}
