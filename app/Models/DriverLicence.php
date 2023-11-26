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

    public static function classifications()
    {
        return [
            'M'=> 'M',
            'A1'=> 'A1',
            'A2'=> 'A2',
            'A'=> 'A',
            'B1'=> 'B1',
            'B'=> 'B',
            'BE'=> 'BE',
            'C1'=> 'C1',
            'C1E'=> 'C1E',
            'C'=> 'C',
            'CE'=> 'CE',
            'D1'=> 'D1',
            'D1E'=> 'D1E',
            'D'=> 'D',
            'DE'=> 'DE',
            'F'=> 'F',
            'G'=> 'G',
        ];
    }
}
