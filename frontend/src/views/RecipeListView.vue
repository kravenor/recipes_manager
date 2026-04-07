<template>
  <div>
    <h1>Tutte le Ricette</h1>
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
import { onMounted } from "vue"
import { useRecipesStore } from "@/stores/recipes.js"

const store = useRecipesStore()
onMounted(() => store.fetchRecipes())
</script>

<style scoped>
.recipes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
.recipe-card { background: white; padding: 1rem; border-radius: 8px; cursor: pointer; }
</style>
