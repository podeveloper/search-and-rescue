<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'brand_id',
        'model_id',
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
        return "{$this->brand->name} {$this->model->name} ({$this->year} - $color) [{$this->licence_plate}]";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(VehicleCategory::class,'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(VehicleBrand::class,'brand_id');
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class,'model_id');
    }

    public static function colors(): array
    {
        $baseColors = [
            'red'    => __('general.red'),
            'green'  => __('general.green'),
            'blue'   => __('general.blue'),
            'yellow' => __('general.yellow'),
            'orange' => __('general.orange'),
            'purple' => __('general.purple'),
            'pink'   => __('general.pink'),
            'brown'  => __('general.brown'),
            'gray'   => __('general.gray'),
            'black'  => __('general.black'),
            'white'  => __('general.white'),
        ];

        $colorCombinations = [];

        foreach ($baseColors as $color1) {
            foreach ($baseColors as $color2) {
                if ($color1 == $color2) continue;

                // Ensure alphabetical order to avoid duplicates
                $combination = ($color1 < $color2) ? "$color1 $color2" : "$color2 $color1";

                $colorCombinations[$combination] = ucwords($combination);
            }
        }

        return array_merge($baseColors, $colorCombinations);
    }
}
