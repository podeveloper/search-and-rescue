<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'is_wearable'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot([
            'brand',
            'color',
            'size',
        ]);
    }

    public static function colors(): array
    {
        $baseColors = [
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


    public static function sizes(): array
    {
        $sizes = [
            'xs'    => 'XS',
            's'  => 'S',
            'm'   => 'M',
            'l' => 'L',
            'xl' => 'XL',
            'xxl' => 'XXL',
            'xxxl'   => 'XXXL',
        ];

        return $sizes;
    }
}