<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create("recipes", function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("slug")->unique();
            $table->text("description")->nullable();
            $table->longText("instructions");
            $table->integer("prep_time")->nullable();
            $table->integer("cook_time")->nullable();
            $table->integer("servings")->nullable();
            $table->string("difficulty")->nullable()->index();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
            $table->string("image_url")->nullable();
            $table->timestamps();
            $table->index("title");
            $table->index("created_at");
        });
    }
    public function down(): void {
        Schema::dropIfExists("recipes");
    }
};

