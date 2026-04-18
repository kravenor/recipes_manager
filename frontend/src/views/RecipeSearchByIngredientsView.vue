<template>
  <div class="search-by-ingredients">
    <h1>🔍 Cerca per Ingredienti</h1>
    <p class="subtitle">Seleziona gli ingredienti che hai a disposizione</p>

    <div class="search-section">
      <div class="filter-row">
        <div class="filter-group">
          <label>Categorie (opzionale)</label>
          <select v-model="selectedCategories" multiple class="category-select">
            <option value="">Tutte le categorie</option>
            <option v-for="cat in categoriesStore.categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
          <small class="help-text">Tieni premuto Ctrl/Cmd per selezionare più categorie</small>
        </div>
      </div>

      <IngredientSelector v-model="selectedIngredients" />
      <button
        @click="searchRecipes"
        :disabled="selectedIngredients.length === 0 || loading"
        class="search-btn"
      >
        {{ loading ? 'Ricerca in corso...' : 'Cerca Ricette' }}
      </button>
    </div>

    <div v-if="selectedIngredients.length > 0 || selectedCategories.length > 0" class="selected-summary">
      <div v-if="selectedIngredients.length > 0" class="selected-section">
        <h3>Ingredienti selezionati:</h3>
        <div class="ingredient-tags">
          <span
            v-for="ing in selectedIngredients"
            :key="ing.id"
            class="ingredient-tag"
          >
            {{ ing.name }}
          </span>
        </div>
      </div>

      <!-- <div v-if="selectedCategories.length > 0" class="selected-section">
        <h3>Categorie selezionate:</h3>
        <div class="ingredient-tags">
          <span
            v-for="catId in selectedCategories"
            :key="catId"
            class="category-tag"
          >
            {{ getCategoryName(catId) }}
          </span>
        </div>
      </div> -->
    </div>

    <div v-if="loading" class="loading">Caricamento ricette...</div>

    <div v-else-if="hasSearched" class="results-section">
      <h2 v-if="recipes.length > 0">
        Trovate {{ recipes.length }} ricette con questi ingredienti
      </h2>
      <h2 v-else>Nessuna ricetta trovata con gli ingredienti selezionati</h2>

      <div v-if="recipes.length > 0" class="recipes-grid">
        <div
          v-for="r in recipes"
          :key="r.id"
          class="recipe-card"
          @click="$router.push(`/recipes/${r.slug}`)"
        >
          <img
            v-if="r.image_url"
            :src="r.image_url"
            :alt="r.title"
            class="recipe-image"
          />
          <div class="recipe-content">
            <h3>{{ r.title }}</h3>
            <p class="description">{{ r.description }}</p>
            <div class="recipe-meta">
              <span v-if="r.prep_time">⏱️ {{ r.prep_time }} min</span>
              <span v-if="r.difficulty" :class="['difficulty', r.difficulty]">
                {{ formatDifficulty(r.difficulty) }}
              </span>
            </div>
            <div class="matching-ingredients">
              <small>Ingredienti che hai:</small>
              <div class="matching-tags">
                <span
                  v-for="ing in getMatchingIngredients(r)"
                  :key="ing.id"
                  class="match-tag"
                >
                  {{ ing.name }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue"
import { useRoute } from "vue-router"
import { useRecipesStore } from "@/stores/recipes.js"
import { useCategoriesStore } from "@/stores/categories.js"
import IngredientSelector from "@/components/IngredientSelector.vue"
import apiClient from "@/api/client.js"

const route = useRoute()
const recipesStore = useRecipesStore()
const categoriesStore = useCategoriesStore()
const selectedIngredients = ref([])
const selectedCategories = ref([])
const recipes = ref([])
const loading = ref(false)
const hasSearched = ref(false)

// Preseleziona categoria se passata via query param
const preselectCategory = () => {
  const categorySlug = route.query.category
  if (categorySlug && categoriesStore.categories.length > 0) {
    const category = categoriesStore.categories.find((c) => c.slug === categorySlug)
    if (category && !selectedCategories.value.includes(category.id)) {
      selectedCategories.value.push(category.id)
    }
  }
}

onMounted(() => {
  categoriesStore.fetchCategories().then(() => {
    preselectCategory()
  })
})

// Se le categorie arrivano dopo o cambia la query, riprova a preselezionare
watch(() => categoriesStore.categories, preselectCategory)
watch(() => route.query.category, preselectCategory)

const searchRecipes = async () => {
  if (selectedIngredients.value.length === 0) return

  loading.value = true
  hasSearched.value = true

  try {
    const ingredientNames = selectedIngredients.value.map((i) => i.name)
    const params = { ingredients: ingredientNames }

    // Aggiungi filtro categorie se selezionate
    if (selectedCategories.value.length > 0) {
      params.category_ids = selectedCategories.value
    }

    const response = await apiClient.get("/recipes", { params })
    recipes.value = response.data.data || response.data
  } catch (err) {
    console.error("Errore nella ricerca:", err)
    recipes.value = []
  } finally {
    loading.value = false
  }
}

const formatDifficulty = (difficulty) => {
  const map = {
    easy: "Facile",
    medium: "Media",
    hard: "Difficile",
  }
  return map[difficulty] || difficulty
}

const getCategoryName = (catId) => {
  const category = categoriesStore.categories.find((c) => c.id === catId)
  return category ? category.name : catId
}

const getMatchingIngredients = (recipe) => {
  if (!recipe.ingredients) return []
  const selectedIds = selectedIngredients.value.map((i) => i.id)
  return recipe.ingredients.filter((ing) =>
    selectedIds.includes(ing.id)
  )
}
</script>

<style scoped>
.search-by-ingredients {
  max-width: 1000px;
  margin: 0 auto;
  padding: 1rem;
}

h1 {
  text-align: center;
  margin-bottom: 0.5rem;
}

.subtitle {
  text-align: center;
  color: #666;
  margin-bottom: 2rem;
}

.search-section {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 1.5rem;
}

.filter-row {
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #eee;
}

.filter-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #555;
}

.category-select {
  width: 100%;
  min-height: 80px;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.category-select option {
  padding: 0.5rem;
  margin: 2px 0;
  border-radius: 4px;
}

.category-select option:checked {
  background: #e65100 linear-gradient(0deg, #e65100 0%, #e65100 100%);
  color: white;
}

.help-text {
  display: block;
  margin-top: 0.25rem;
  color: #666;
  font-size: 0.8rem;
}

.search-btn {
  width: 100%;
  padding: 0.75rem;
  background: #e65100;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  margin-top: 1rem;
}

.search-btn:hover:not(:disabled) {
  background: #d84315;
}

.search-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.selected-summary {
  background: #e3f2fd;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.selected-summary h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
}

.ingredient-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.ingredient-tag {
  background: #1976d2;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.875rem;
}

.category-tag {
  background: #e65100;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.875rem;
}

.selected-section {
  margin-bottom: 1rem;
}

.selected-section:last-child {
  margin-bottom: 0;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.results-section h2 {
  margin-bottom: 1rem;
}

.recipes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.recipe-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.recipe-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.recipe-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.recipe-content {
  padding: 1rem;
}

.recipe-content h3 {
  margin: 0 0 0.5rem 0;
  color: #333;
}

.description {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 0.75rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.recipe-meta {
  display: flex;
  gap: 1rem;
  margin-bottom: 0.75rem;
  font-size: 0.85rem;
  color: #666;
}

.difficulty {
  padding: 0.125rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}

.difficulty.easy {
  background: #e8f5e9;
  color: #2e7d32;
}

.difficulty.medium {
  background: #fff3e0;
  color: #ef6c00;
}

.difficulty.hard {
  background: #ffebee;
  color: #c62828;
}

.matching-ingredients {
  border-top: 1px solid #eee;
  padding-top: 0.75rem;
}

.matching-ingredients small {
  color: #888;
  display: block;
  margin-bottom: 0.25rem;
}

.matching-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
}

.match-tag {
  background: #c8e6c9;
  color: #2e7d32;
  padding: 0.125rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
}
</style>