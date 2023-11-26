<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RadioCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'call_sign',
        'radio_net_sign',
        'licence_number',
        'licence_class',
        'date_of_issue',
        'expiration_date',
        'user_id',
    ];

    protected $dates = ['date_of_issue', 'expiration_date', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
