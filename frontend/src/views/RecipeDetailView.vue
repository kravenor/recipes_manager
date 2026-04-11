<template>
  <div v-if="store.currentRecipe" class="recipe-detail">
    <h1>{{ store.currentRecipe.title }}</h1>

    <p v-if="store.currentRecipe.description" class="description">{{ store.currentRecipe.description }}</p>

    <div class="meta">
      <span v-if="store.currentRecipe.prep_time" class="meta-item">
        ⏱️ {{ store.currentRecipe.prep_time }} min prep
      </span>
      <span v-if="store.currentRecipe.cook_time" class="meta-item">
        🔥 {{ store.currentRecipe.cook_time }} min cottura
      </span>
      <span v-if="store.currentRecipe.servings" class="meta-item">
        🍽️ {{ store.currentRecipe.servings }} porzioni
      </span>
      <span v-if="store.currentRecipe.difficulty" class="meta-item">
        📊 {{ formatDifficulty(store.currentRecipe.difficulty) }}
      </span>
    </div>

    <div class="content-grid">
      <div class="ingredients-section" v-if="store.currentRecipe.ingredients?.length">
        <h2>🥘 Ingredienti</h2>
        <ul class="ingredients-list">
          <li v-for="ing in store.currentRecipe.ingredients" :key="ing.id" class="ingredient-item">
            <span class="ingredient-name">{{ ing.name }}</span>
            <span v-if="ing.quantity || ing.unit" class="ingredient-qty">
              <span v-if="ing.quantity">{{ formatQuantity(ing.quantity) }}</span>
              <span v-if="ing.unit">{{ ing.unit }}</span>
            </span>
            <span v-if="ing.notes" class="ingredient-notes">({{ ing.notes }})</span>
          </li>
        </ul>
      </div>

      <div class="instructions-section">
        <h2>📖 Istruzioni</h2>
        <div class="instructions-text">{{ store.currentRecipe.instructions }}</div>
      </div>
    </div>

    <div class="actions">
      <button @click="$router.back()" class="btn-secondary">← Indietro</button>
    </div>
  </div>

  <div v-else-if="store.loading" class="loading">Caricamento...</div>

  <div v-else class="error">
    <h2>Ricetta non trovata</h2>
    <router-link to="/recipes">Torna alle ricette</router-link>
  </div>
</template>

<script setup>
import { onMounted } from "vue"
import { useRoute } from "vue-router"
import { useRecipesStore } from "@/stores/recipes.js"

const route = useRoute()
const store = useRecipesStore()

onMounted(() => store.fetchRecipe(route.params.slug))

const formatDifficulty = (diff) => {
  const map = { easy: "Facile", medium: "Media", hard: "Difficile" }
  return map[diff] || diff
}

const formatQuantity = (qty) => {
  // Remove trailing zeros if it's a whole number
  return Number.isInteger(qty) ? qty.toString() : qty.toFixed(2).replace(/\.?0+$/, "")
}
</script>

<style scoped>
.recipe-detail {
  max-width: 900px;
  margin: 0 auto;
}

h1 {
  margin-bottom: 1rem;
  color: #333;
}

.description {
  color: #666;
  font-size: 1.125rem;
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 2rem;
}

.meta-item {
  background: #fff3e0;
  color: #e65100;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 500;
}

.content-grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 2rem;
}

@media (max-width: 768px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}

.ingredients-section,
.instructions-section {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

h2 {
  margin-bottom: 1rem;
  color: #333;
  font-size: 1.25rem;
}

.ingredients-list {
  list-style: none;
}

.ingredient-item {
  padding: 0.75rem 0;
  border-bottom: 1px solid #e0e0e0;
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  align-items: center;
}

.ingredient-item:last-child {
  border-bottom: none;
}

.ingredient-name {
  flex: 1;
  font-weight: 500;
}

.ingredient-qty {
  color: #e65100;
  font-weight: 500;
  display: flex;
  gap: 0.25rem;
}

.ingredient-notes {
  color: #666;
  font-size: 0.875rem;
  font-style: italic;
}

.instructions-text {
  line-height: 1.8;
  white-space: pre-line;
}

.actions {
  margin-top: 2rem;
  display: flex;
  gap: 1rem;
}

.btn-secondary {
  padding: 0.75rem 1.5rem;
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  color: #666;
}

.btn-secondary:hover {
  background: #f5f5f5;
}

.loading,
.error {
  text-align: center;
  padding: 3rem;
}

.error {
  color: #d32f2f;
}
</style>
