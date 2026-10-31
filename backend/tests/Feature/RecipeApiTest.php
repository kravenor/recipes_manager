<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeApiTest extends TestCase
{
    use RefreshDatabase;

    private function recipe(string $title, array $ingredients = [], array $attributes = []): Recipe
    {
        $recipe = Recipe::create(array_merge([
            "title" => $title,
            "instructions" => "Cuoci.",
        ], $attributes));

        foreach ($ingredients as $name) {
            $recipe->ingredients()->attach(Ingredient::firstOrCreate(["name" => $name]));
        }

        return $recipe;
    }

    public function test_index_returns_paginated_recipes_with_meta(): void
    {
        $this->recipe("Pasta al pomodoro");
        $this->recipe("Risotto");

        $this->getJson("/api/recipes")
            ->assertOk()
            ->assertJsonCount(2, "data")
            ->assertJsonStructure(["data" => [["id", "title", "slug", "ingredients", "categories", "tags"]], "meta", "links"]);
    }

    public function test_index_filters_by_all_ingredients(): void
    {
        $this->recipe("Caprese", ["pomodoro", "basilico", "mozzarella"]);
        $this->recipe("Sugo", ["pomodoro", "aglio"]);
        $this->recipe("Pesto", ["basilico", "aglio"]);

        $this->getJson("/api/recipes?ingredients[]=pomodoro&ingredients[]=basilico")
            ->assertOk()
            ->assertJsonCount(1, "data")
            ->assertJsonPath("data.0.title", "Caprese");
    }

    public function test_index_accepts_single_ingredient_as_string(): void
    {
        $this->recipe("Sugo", ["pomodoro"]);
        $this->recipe("Pesto", ["basilico"]);

        $this->getJson("/api/recipes?ingredients=pomodoro")
            ->assertJsonCount(1, "data")
            ->assertJsonPath("data.0.title", "Sugo");
    }

    public function test_index_filters_by_difficulty(): void
    {
        $this->recipe("Facile", [], ["difficulty" => "easy"]);
        $this->recipe("Difficile", [], ["difficulty" => "hard"]);

        $this->getJson("/api/recipes?difficulty=hard")
            ->assertJsonCount(1, "data")
            ->assertJsonPath("data.0.title", "Difficile");
    }

    public function test_index_filters_by_category_slug_and_ids(): void
    {
        $primi = Category::create(["name" => "Primi"]);
        $dolci = Category::create(["name" => "Dolci"]);
        $this->recipe("Carbonara")->categories()->attach($primi);
        $this->recipe("Tiramisù")->categories()->attach($dolci);

        $this->getJson("/api/recipes?category=primi")
            ->assertJsonCount(1, "data")
            ->assertJsonPath("data.0.title", "Carbonara");

        $this->getJson("/api/recipes?category_ids[]={$dolci->id}")
            ->assertJsonCount(1, "data")
            ->assertJsonPath("data.0.title", "Tiramisù");

        // Empty ids are ignored instead of filtering everything out
        $this->getJson("/api/recipes?category_ids[]=")
            ->assertJsonCount(2, "data");
    }

    public function test_show_returns_recipe_by_slug(): void
    {
        $this->recipe("Pasta al pomodoro", ["pomodoro"]);

        $this->getJson("/api/recipes/pasta-al-pomodoro")
            ->assertOk()
            ->assertJsonPath("data.title", "Pasta al pomodoro")
            ->assertJsonPath("data.ingredients.0.name", "pomodoro");
    }

    public function test_show_returns_404_for_unknown_slug(): void
    {
        $this->getJson("/api/recipes/non-esiste")->assertNotFound();
    }

    public function test_store_creates_recipe_with_relations(): void
    {
        $pomodoro = Ingredient::create(["name" => "pomodoro"]);
        $category = Category::create(["name" => "Primi"]);
        $tag = Tag::create(["name" => "Veloce"]);

        $this->postJson("/api/recipes", [
            "title" => "Pasta al pomodoro",
            "instructions" => "Bolli la pasta.",
            "difficulty" => "easy",
            "ingredients" => [["id" => $pomodoro->id, "quantity" => 200, "unit" => "g"]],
            "category_ids" => [$category->id],
            "tag_ids" => [$tag->id],
        ])
            ->assertCreated()
            ->assertJsonPath("data.slug", "pasta-al-pomodoro")
            ->assertJsonPath("data.ingredients.0.unit", "g")
            ->assertJsonPath("data.categories.0.id", $category->id)
            ->assertJsonPath("data.tags.0.id", $tag->id);

        $this->assertDatabaseHas("recipe_ingredient", ["ingredient_id" => $pomodoro->id, "quantity" => 200]);
    }

    public function test_store_generates_unique_slugs(): void
    {
        $first = $this->postJson("/api/recipes", ["title" => "Lasagna", "instructions" => "x"])->json("data.slug");
        $second = $this->postJson("/api/recipes", ["title" => "Lasagna", "instructions" => "x"])->json("data.slug");

        $this->assertSame("lasagna", $first);
        $this->assertNotSame($first, $second);
    }

    public function test_store_validates_input(): void
    {
        $this->postJson("/api/recipes", [
            "difficulty" => "impossible",
            "image_url" => "not-a-url",
            "ingredients" => [["id" => 999]],
            "category_ids" => [999],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(["title", "instructions", "difficulty", "image_url", "ingredients.0.id", "category_ids.0"]);
    }

    public function test_update_changes_fields(): void
    {
        $recipe = $this->recipe("Vecchio titolo");

        $this->putJson("/api/recipes/{$recipe->id}", ["title" => "Nuovo titolo", "servings" => 4])
            ->assertOk()
            ->assertJsonPath("data.title", "Nuovo titolo")
            ->assertJsonPath("data.servings", 4);
    }

    public function test_update_validates_and_handles_missing(): void
    {
        $recipe = $this->recipe("Ricetta");

        $this->putJson("/api/recipes/{$recipe->id}", ["servings" => 0])
            ->assertJsonValidationErrors(["servings"]);

        $this->putJson("/api/recipes/999", ["title" => "x"])->assertNotFound();
    }

    public function test_destroy_deletes_recipe_and_pivots(): void
    {
        $recipe = $this->recipe("Da eliminare", ["sale"]);

        $this->deleteJson("/api/recipes/{$recipe->id}")->assertOk();

        $this->assertModelMissing($recipe);
        $this->assertDatabaseCount("recipe_ingredient", 0);
        $this->deleteJson("/api/recipes/{$recipe->id}")->assertNotFound();
    }

    public function test_search_without_query_falls_back_to_index(): void
    {
        $this->recipe("Sugo", ["pomodoro"]);
        $this->recipe("Pesto", ["basilico"]);

        $this->getJson("/api/recipes/search?ingredients[]=basilico")
            ->assertOk()
            ->assertJsonCount(1, "data")
            ->assertJsonPath("data.0.title", "Pesto");
    }

    public function test_search_with_query_uses_scout(): void
    {
        // Scout "null" driver in tests: verifies the endpoint wiring, not Elasticsearch relevance
        $this->recipe("Sugo");

        $this->getJson("/api/recipes/search?q=sugo")
            ->assertOk()
            ->assertJsonStructure(["data", "meta"]);
    }
}
