<template>
  <div>
    <h1>Risultati per "{{ route.query.q }}"</h1>
    <div v-if="store.loading">Ricerca in corso...</div>
    <div v-else-if="store.recipes.length === 0">Nessun risultato</div>
    <div v-else class="recipes-grid">
      <div v-for="r in store.recipes" :key="r.id" class="recipe-card" @click="$router.push(`/recipes/${r.slug}`)">
        <h3>{{ r.title }}</h3>
        <p>{{ r.description }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, watch } from "vue"
import { useRoute } from "vue-router"
import { useRecipesStore } from "@/stores/recipes.js"

const route = useRoute()
const store = useRecipesStore()

const search = () => {
  if (route.query.q) {
    store.searchRecipes(route.query.q)
  }
}

onMounted(search)
watch(() => route.query.q, search)
</script>

<style scoped>
.recipes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem; }
.recipe-card { background: white; padding: 1rem; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.recipe-card:hover { transform: translateY(-2px); }
</style>
