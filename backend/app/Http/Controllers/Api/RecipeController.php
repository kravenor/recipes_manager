<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Http\Resources\RecipeResource;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with(["ingredients", "categories", "tags"]);
        
        if ($request->has("category")) {
            $query->whereHas("categories", function ($q) use ($request) {
                $q->where("slug", $request->category);
            });
        }
        
        if ($request->has("difficulty")) {
            $query->where("difficulty", $request->difficulty);
        }
        
        if ($request->has("ingredients")) {
            $ingredients = $request->ingredients;
            $query->whereHas("ingredients", function ($q) use ($ingredients) {
                $q->whereIn("name", $ingredients);
            });
        }
        
        $recipes = $query->paginate(15);
        
        return RecipeResource::collection($recipes);
    }

    public function show($slug)
    {
        $recipe = Recipe::with(["ingredients", "categories", "tags", "user"])
            ->where("slug", $slug)
            ->firstOrFail();
            
        return new RecipeResource($recipe);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "instructions" => "required|string",
            "prep_time" => "nullable|integer|min:0",
            "cook_time" => "nullable|integer|min:0",
            "servings" => "nullable|integer|min:1",
            "difficulty" => "nullable|string|in:easy,medium,hard",
            "image_url" => "nullable|string|url",
            "ingredients" => "nullable|array",
            "ingredients.*.id" => "required_with:ingredients|exists:ingredients,id",
            "ingredients.*.quantity" => "nullable|numeric",
            "ingredients.*.unit" => "nullable|string",
            "ingredients.*.notes" => "nullable|string",
            "category_ids" => "nullable|array",
            "category_ids.*" => "exists:categories,id",
            "tag_ids" => "nullable|array",
            "tag_ids.*" => "exists:tags,id",
        ]);

        $recipe = Recipe::create($validated);
        
        if (isset($validated["ingredients"])) {
            $ingredientsData = collect($validated["ingredients"])->mapWithKeys(function ($item) {
                return [$item["id"] => [
                    "quantity" => $item["quantity"] ?? null,
                    "unit" => $item["unit"] ?? null,
                    "notes" => $item["notes"] ?? null,
                ]];
            });
            $recipe->ingredients()->attach($ingredientsData);
        }
        
        if (isset($validated["category_ids"])) {
            $recipe->categories()->attach($validated["category_ids"]);
        }
        
        if (isset($validated["tag_ids"])) {
            $recipe->tags()->attach($validated["tag_ids"]);
        }

        return new RecipeResource($recipe->load(["ingredients", "categories", "tags"]));
    }

    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);
        
        $validated = $request->validate([
            "title" => "sometimes|string|max:255",
            "description" => "nullable|string",
            "instructions" => "sometimes|string",
            "prep_time" => "nullable|integer|min:0",
            "cook_time" => "nullable|integer|min:0",
            "servings" => "nullable|integer|min:1",
            "difficulty" => "nullable|string|in:easy,medium,hard",
            "image_url" => "nullable|string|url",
        ]);

        $recipe->update($validated);

        return new RecipeResource($recipe->fresh()->load(["ingredients", "categories", "tags"]));
    }

    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->delete();
        
        return response()->json(["message" => "Recipe deleted successfully"]);
    }

    public function search(Request $request)
    {
        $query = $request->input("q");
        
        if (empty($query)) {
            return $this->index($request);
        }
        
        $recipes = Recipe::search($query)
            ->query(fn ($q) => $q->with(["ingredients", "categories", "tags"]))
            ->paginate(15);
            
        return RecipeResource::collection($recipes);
    }
}
