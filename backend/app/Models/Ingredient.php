<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Ingredient extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ["name", "category"];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, "recipe_ingredient")
            ->withPivot("quantity", "unit", "notes");
    }

    public function toSearchableArray(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "category" => $this->category,
        ];
    }
}
