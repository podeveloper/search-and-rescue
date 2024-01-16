<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'medications',
        'allergies',
        'medical_conditions',
        'vision_aids',
        'prosthetics',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_name_2',
        'emergency_contact_phone_2',
        'blood_type',
        'other_health_information',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function bloodTypes()
    {
        return [
            "A+" => "A+",
            "A-" => "A-",
            "B+" => "B+",
            "B-" => "B-",
            "AB+" => "AB+",
            "AB-" => "AB-",
            "0+" => "0+",
            "0-" => "0-"
        ];
    }
}
