<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'brand',
        'model',
        'year',
        'color',
        'licence_plate',
        'vin',
        'mileage',
        'user_id',
    ];

    public function getCombinedAttribute()
    {
        $color = ucwords($this->color);
        return "{$this->brand} {$this->model} ({$this->year} - $color) [{$this->licence_plate}]";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function colors(): array
    {
        return [
            'red'    => 'Red',
            'green'  => 'Green',
            'blue'   => 'Blue',
            'yellow' => 'Yellow',
            'orange' => 'Orange',
            'purple' => 'Purple',
            'pink'   => 'Pink',
            'brown'  => 'Brown',
            'gray'   => 'Gray',
            'black'  => 'Black',
            'white'  => 'White',
        ];
    }
}
