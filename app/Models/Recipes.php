<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipes extends Model
{
    protected $fillable = [
        'name',
        'ingredients',
        'instructions',
        'prepTimeMinutes',
        'cookTimeMinutes',
        'servings',
        'difficulty',
        'cuisine',
        'caloriesPerServing',
        'image'
    ];
}
