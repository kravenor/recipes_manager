<?php

use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get("/recipes", [RecipeController::class, "index"]);
Route::get("/recipes/search", [RecipeController::class, "search"]);
Route::get("/recipes/{slug}", [RecipeController::class, "show"]);
Route::post("/recipes", [RecipeController::class, "store"]);
Route::put("/recipes/{id}", [RecipeController::class, "update"]);
Route::delete("/recipes/{id}", [RecipeController::class, "destroy"]);

Route::get("/ingredients", [IngredientController::class, "index"]);
Route::get("/ingredients/search", [IngredientController::class, "search"]);

Route::get("/categories", [CategoryController::class, "index"]);

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);
Route::post("/logout", [AuthController::class, "logout"]);

