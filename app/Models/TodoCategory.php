<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Shibomb\FilamentTodo\Models\Todo;

class TodoCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'todo_categories';

    protected $fillable = [
        'name',
        'color',
        'sort_order',
    ];


    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class, 'category_id', 'id');
    }
}
