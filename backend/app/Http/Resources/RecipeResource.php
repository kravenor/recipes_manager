<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "slug" => $this->slug,
            "description" => $this->description,
            "instructions" => $this->instructions,
            "prep_time" => $this->prep_time,
            "cook_time" => $this->cook_time,
            "servings" => $this->servings,
            "difficulty" => $this->difficulty,
            "image_url" => $this->image_url,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "ingredients" => $this->whenLoaded("ingredients", function () {
                return $this->ingredients->map(function ($ingredient) {
                    return [
                        "id" => $ingredient->id,
                        "name" => $ingredient->name,
                        "category" => $ingredient->category,
                        "quantity" => $ingredient->pivot->quantity,
                        "unit" => $ingredient->pivot->unit,
                        "notes" => $ingredient->pivot->notes,
                    ];
                });
            }),
            "categories" => $this->whenLoaded("categories"),
            "tags" => $this->whenLoaded("tags"),
            "user" => $this->whenLoaded("user"),
        ];
    }
}
