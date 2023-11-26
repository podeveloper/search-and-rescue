<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FirstAidCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'licence_number',
        'training_institution',
        'date_of_issue',
        'expiration_date',
        'pdf',
        'user_id',
    ];

    protected $dates = [
        'date_of_issue',
        'expiration_date',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
