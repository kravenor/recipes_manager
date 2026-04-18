<template>
  <div class="category-recipes">
    <div class="categories-nav">
      <h2>📂 Categorie</h2>
      <div v-if="categoriesStore.loading" class="loading">Caricamento...</div>
      <div v-else class="categories-list">
        <router-link
          v-for="cat in categoriesStore.categories"
          :key="cat.id"
          :to="`/categories/${cat.slug}`"
          :class="['category-item', { active: currentCategory === cat.slug }]"
        >
          <span class="category-name">{{ cat.name }}</span>
          <span v-if="cat.description" class="category-desc">{{ cat.description }}</span>
        </router-link>
      </div>
    </div>

    <div class="recipes-section">
      <div v-if="currentCategoryName" class="section-header">
        <h1>{{ currentCategoryName }}</h1>
        <router-link
          :to="{ path: '/search-by-ingredients', query: { category: currentCategory } }"
          class="btn-filter"
        >
          🔍 Cerca in questa categoria
        </router-link>
      </div>
      <div v-else class="section-header">
        <h1>Tutte le ricette</h1>
      </div>

      <div v-if="recipesStore.loading" class="loading">Caricamento ricette...</div>

      <div v-else-if="recipesStore.recipes.length === 0" class="no-results"
        >Nessuna ricetta trovata in questa categoria.</div
      >

      <div v-else class="recipes-grid">
        <div
          v-for="r in recipesStore.recipes"
          :key="r.id"
          class="recipe-card"
          @click="$router.push(`/recipes/${r.slug}`)"
        >
          <img v-if="r.image_url" :src="r.image_url" :alt="r.title" class="recipe-image" />
          <div class="recipe-content">
            <h3>{{ r.title }}</h3>
            <p class="description">{{ r.description }}</p>

            <div class="recipe-meta">
              <span v-if="r.prep_time">⏱️ {{ r.prep_time }} min</span>
              <span v-if="r.difficulty" :class="['difficulty', r.difficulty]"
                >{{ formatDifficulty(r.difficulty) }}</span
              >
            </div>

            <div v-if="r.categories && r.categories.length > 0" class="recipe-categories">
              <span v-for="cat in r.categories" :key="cat.id" class="category-tag">{{ cat.name }}
                </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Paginazione -->
      <div v-if="recipesStore.pagination.last_page > 1" class="pagination"
        >
        <button
          :disabled="recipesStore.pagination.current_page === 1"
          @click="changePage(recipesStore.pagination.current_page - 1)"
          class="page-btn"
          >< Prev</button
        >

        <span class="page-info"
          >Pagina {{ recipesStore.pagination.current_page }} di
          {{ recipesStore.pagination.last_page }}</span
        >

        <button
          :disabled="recipesStore.pagination.current_page === recipesStore.pagination.last_page"
          @click="changePage(recipesStore.pagination.current_page + 1)"
          class="page-btn"
          >Next ></button
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, watch } from "vue"
import { useRoute, useRouter } from "vue-router"
import { useRecipesStore } from "@/stores/recipes.js"
import { useCategoriesStore } from "@/stores/categories.js"

const route = useRoute()
const router = useRouter()
const recipesStore = useRecipesStore()
const categoriesStore = useCategoriesStore()

const currentCategory = computed(() => route.params.slug)

const currentCategoryName = computed(() => {
  if (!currentCategory.value) return null
  const cat = categoriesStore.categories.find((c) => c.slug === currentCategory.value)
  return cat ? cat.name : "Categoria"
})

const formatDifficulty = (difficulty) => {
  const map = {
    easy: "Facile",
    medium: "Media",
    hard: "Difficile",
  }
  return map[difficulty] || difficulty
}

const loadRecipes = () => {
  const params = {}
  if (currentCategory.value) {
    params.category = currentCategory.value
  }
  recipesStore.fetchRecipes(params)
}

const changePage = (page) => {
  const params = { page }
  if (currentCategory.value) {
    params.category = currentCategory.value
  }
  recipesStore.fetchRecipes(params)
  window.scrollTo({ top: 0, behavior: "smooth" })
}

onMounted(() => {
  categoriesStore.fetchCategories()
  loadRecipes()
})

watch(currentCategory, () => {
  loadRecipes()
})
</script>

<style scoped>
.category-recipes {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 2rem;
  max-width: 1400px;
  margin: 0 auto;
  padding: 1rem;
}

@media (max-width: 768px) {
  .category-recipes {
    grid-template-columns: 1fr;
  }
}

.categories-nav {
  background: white;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  height: fit-content;
}

.categories-nav h2 {
  margin: 0 0 1rem 0;
  font-size: 1.25rem;
}

.categories-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.category-item {
  display: flex;
  flex-direction: column;
  padding: 0.75rem;
  border-radius: 4px;
  text-decoration: none;
  color: #333;
  transition: background 0.2s;
}

.category-item:hover {
  background: #f5f5f5;
}

.category-item.active {
  background: #e65100;
  color: white;
}

.category-name {
  font-weight: 500;
}

.category-desc {
  font-size: 0.8rem;
  color: #666;
  margin-top: 0.25rem;
}

.category-item.active .category-desc {
  color: rgba(255, 255, 255, 0.8);
}

.recipes-section {
  min-height: 500px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-header h1 {
  margin: 0;
}

.btn-filter {
  padding: 0.5rem 1rem;
  background: #e65100;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  font-size: 0.9rem;
}

.btn-filter:hover {
  background: #d84315;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.no-results {
  text-align: center;
  padding: 2rem;
  color: #666;
  background: white;
  border-radius: 8px;
}

.recipes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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

.recipe-categories {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
  margin-top: 0.5rem;
}

.category-tag {
  background: #e3f2fd;
  color: #1976d2;
  padding: 0.125rem 0.5rem;
  border-radius: 12px;
  font-size: 0.75rem;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
  padding: 1rem;
}

.page-btn {
  padding: 0.5rem 1rem;
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
}

.page-btn:hover:not(:disabled) {
  background: #f5f5f5;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: #666;
}
</style>
