<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'content',
    ];

    /**
     * Get the sender of the email.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipients of the email.
     */
    public function recipients()
    {
        return $this->belongsToMany(User::class, 'email_recipients', 'sent_email_id', 'user_id')
            ->withTimestamps();
    }
}
