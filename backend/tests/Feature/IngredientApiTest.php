<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_ingredients_sorted_and_filtered_by_category(): void
    {
        Ingredient::create(["name" => "zucchina", "category" => "verdura"]);
        Ingredient::create(["name" => "aglio", "category" => "verdura"]);
        Ingredient::create(["name" => "pecorino", "category" => "formaggi"]);

        $this->getJson("/api/ingredients")
            ->assertOk()
            ->assertJsonPath("total", 3)
            ->assertJsonPath("data.0.name", "aglio");

        $this->getJson("/api/ingredients?category=verdura")
            ->assertJsonPath("total", 2);
    }

    public function test_search_without_query_falls_back_to_index(): void
    {
        Ingredient::create(["name" => "sale"]);

        $this->getJson("/api/ingredients/search")
            ->assertOk()
            ->assertJsonPath("data.0.name", "sale");
    }

    public function test_store_normalizes_name(): void
    {
        $this->postJson("/api/ingredients", ["name" => "  Basilico ", "category" => "erbe"])
            ->assertCreated()
            ->assertJsonPath("name", "basilico");

        $this->assertDatabaseHas("ingredients", ["name" => "basilico", "category" => "erbe"]);
    }

    public function test_store_rejects_duplicates_and_missing_name(): void
    {
        Ingredient::create(["name" => "sale"]);

        $this->postJson("/api/ingredients", ["name" => "sale"])
            ->assertJsonValidationErrors(["name"]);
        $this->postJson("/api/ingredients", ["name" => " SALE "])
            ->assertJsonValidationErrors(["name"]);
        $this->postJson("/api/ingredients", [])
            ->assertJsonValidationErrors(["name"]);
    }

    public function test_find_or_create_creates_then_finds(): void
    {
        $created = $this->postJson("/api/ingredients/find-or-create", ["name" => " Origano "])
            ->assertCreated()
            ->assertJsonPath("name", "origano");

        $this->postJson("/api/ingredients/find-or-create", ["name" => "ORIGANO"])
            ->assertOk()
            ->assertJsonPath("id", $created->json("id"));

        $this->assertDatabaseCount("ingredients", 1);
    }

    public function test_categories_are_listed_alphabetically(): void
    {
        Category::create(["name" => "Primi"]);
        Category::create(["name" => "Antipasti"]);

        $this->getJson("/api/categories")
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath("0.name", "Antipasti")
            ->assertJsonPath("1.slug", "primi");
    }
}
