<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverLicence extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pdf',
        'class',
        'date_of_issue',
        'expiration_date',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
