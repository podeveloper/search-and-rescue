<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForestFireFightingCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'work_area_city_id',
        'directorate',
        'duty',
        'date_of_issue',
        'expiration_date',
        'pdf',
        'user_id',
    ];

    protected $dates = ['date_of_issue', 'expiration_date', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'work_area_city_id')->where('country_id','=',225); // Set Turkey as Default
    }

    public static function duties()
    {
        return [
            'Gönüllü' => 'Gönüllü',
        ];
    }
}
