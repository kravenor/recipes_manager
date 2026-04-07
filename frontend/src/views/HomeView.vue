<template>
  <div class="home">
    <h1>🍳 Ricette</h1>
    <p>Cerca e gestisci le tue ricette culinarie</p>
    <div class="search-box">
      <input v-model="query" @keyup.enter="search" placeholder="Cerca ricette..." />
      <button @click="search">Cerca</button>
    </div>
    <div v-if="store.loading">Caricamento...</div>
    <div v-else class="recipes-grid">
      <div v-for="r in store.recipes" :key="r.id" class="recipe-card" @click="$router.push(`/recipes/${r.slug}`)">
        <h3>{{ r.title }}</h3>
        <p>{{ r.description }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useRouter } from "vue-router"
import { useRecipesStore } from "@/stores/recipes.js"

const router = useRouter()
const store = useRecipesStore()
const query = ref("")

onMounted(() => store.fetchRecipes())

const search = () => router.push({ path: "/search", query: { q: query.value } })
</script>

<style scoped>
.home { text-align: center; }
h1 { font-size: 2.5rem; margin-bottom: 1rem; }
.search-box { margin: 2rem 0; display: flex; gap: 0.5rem; justify-content: center; }
input { padding: 0.75rem; width: 300px; border: 1px solid #ddd; border-radius: 4px; }
button { padding: 0.75rem 1.5rem; background: #e65100; color: white; border: none; border-radius: 4px; cursor: pointer; }
.recipes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; margin-top: 2rem; }
.recipe-card { background: white; padding: 1rem; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.recipe-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
</style>
