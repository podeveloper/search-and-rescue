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


    public static function sizes(): array
    {
        $numericSizes = range(35, 60);

        $sizes = [
            'xs'   => 'XS',
            's'    => 'S',
            'm'    => 'M',
            'l'    => 'L',
            'xl'   => 'XL',
            'xxl'  => 'XXL',
            'xxxl' => 'XXXL',
        ];

        $sizes += array_combine(array_map('strval', $numericSizes), $numericSizes);

        return $sizes;
    }
}
