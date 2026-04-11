<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index(Request $request)
    {
        $query = Ingredient::query();
        
        if ($request->has("category")) {
            $query->where("category", $request->category);
        }
        
        $ingredients = $query->orderBy("name")->paginate(50);
        
        return response()->json($ingredients);
    }

    public function search(Request $request)
    {
        $query = $request->input("q");

        if (empty($query)) {
            return $this->index($request);
        }

        $ingredients = Ingredient::search($query)
            ->paginate(20);

        return response()->json($ingredients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255|unique:ingredients,name",
            "category" => "nullable|string|max:100",
        ]);

        // Normalize: lowercase, trim
        $validated["name"] = strtolower(trim($validated["name"]));

        $ingredient = Ingredient::create($validated);

        return response()->json($ingredient, 201);
    }

    public function findOrCreate(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "category" => "nullable|string|max:100",
        ]);

        $name = strtolower(trim($validated["name"]));

        // Find existing or create new
        $ingredient = Ingredient::firstOrCreate(
            ["name" => $name],
            ["category" => $validated["category"] ?? null]
        );

        return response()->json($ingredient, $ingredient->wasRecentlyCreated ? 201 : 200);
    }
}
