<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Laravel\Scout\Searchable;

class Recipe extends Model
{
    use HasFactory, Sluggable, Searchable;

    protected $fillable = [
        "title",
        "slug",
        "description",
        "instructions",
        "prep_time",
        "cook_time",
        "servings",
        "difficulty",
        "user_id",
        "image_url",
    ];

    public function sluggable(): array
    {
        return [
            "slug" => [
                "source" => "title"
            ]
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, "recipe_ingredient")
            ->withPivot("quantity", "unit", "notes");
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, "recipe_category");
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, "recipe_tag");
    }

    public function toSearchableArray(): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "instructions" => $this->instructions,
            "ingredients" => $this->ingredients->pluck("name")->join(" "),
            "slug" => $this->slug,
            "difficulty" => $this->difficulty,
        ];
    }
}
