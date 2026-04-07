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
}
