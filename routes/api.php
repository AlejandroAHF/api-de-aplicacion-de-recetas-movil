<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecipesController;

Route::group(["prefix"=>"user"],function($router){
    Route::get('/',[UserController::class,'getUsers']);
    Route::post('/',[UserController::class,'createUser']);
});

Route::group(["prefix"=>"recipes"],function($router){
    Route::get('/',[RecipesController::class,'getRecipes']);
    Route::post('/',[RecipesController::class,'postRecipe']);
    Route::put('/{id}',[RecipesController::class,'updateRecipe']);
    Route::delete('/{id}',[RecipesController::class,'deleteRecipe']);
});

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */
