<template>
  <div v-if="store.currentRecipe" class="recipe-detail">
    <h1>{{ store.currentRecipe.title }}</h1>
    <p v-if="store.currentRecipe.description">{{ store.currentRecipe.description }}</p>
    
    <div class="meta" v-if="store.currentRecipe.prep_time || store.currentRecipe.difficulty">
      <span v-if="store.currentRecipe.prep_time">⏱️ {{ store.currentRecipe.prep_time }} min</span>
      <span v-if="store.currentRecipe.difficulty">📊 {{ store.currentRecipe.difficulty }}</span>
    </div>
    
    <h2 v-if="store.currentRecipe.ingredients?.length">Ingredienti</h2>
    <ul>
      <li v-for="i in store.currentRecipe.ingredients" :key="i.id">
        {{ i.name }} <span v-if="i.quantity">- {{ i.quantity }} {{ i.unit }}</span>
      </li>
    </ul>
    
    <h2>Istruzioni</h2>
    <div class="instructions">{{ store.currentRecipe.instructions }}</div>
    
    <button @click="$router.back()">← Indietro</button>
  </div>
  <div v-else-if="store.loading">Caricamento...</div>
  <div v-else>Ricetta non trovata</div>
</template>

<script setup>
import { onMounted } from "vue"
import { useRoute } from "vue-router"
import { useRecipesStore } from "@/stores/recipes.js"

const route = useRoute()
const store = useRecipesStore()

onMounted(() => store.fetchRecipe(route.params.slug))
</script>

<style scoped>
.recipe-detail { max-width: 800px; margin: 0 auto; }
.meta { margin: 1rem 0; display: flex; gap: 1rem; }
.instructions { white-space: pre-line; line-height: 1.6; margin: 1rem 0; }
button { padding: 0.5rem 1rem; background: #e65100; color: white; border: none; border-radius: 4px; cursor: pointer; }
</style>
