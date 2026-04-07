<template>
  <div class="create-form">
    <h1>Nuova Ricetta</h1>
    
    <form @submit.prevent="submit">
      <div class="form-group">
        <label>Titolo *</label>
        <input v-model="form.title" required />
      </div>
      
      <div class="form-group">
        <label>Descrizione</label>
        <textarea v-model="form.description" rows="3"></textarea>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label>Tempo prep (min)</label>
          <input v-model="form.prep_time" type="number" />
        </div>
        
        <div class="form-group">
          <label>Porzioni</label>
          <input v-model="form.servings" type="number" />
        </div>
      </div>
      
      <div class="form-group">
        <label>Istruzioni *</label>
        <textarea v-model="form.instructions" rows="8" required></textarea>
      </div>
      
      <div class="buttons">
        <button type="button" @click="$router.back()">Annulla</button>
        <button type="submit" :disabled="store.loading">{{ store.loading ? "Salvataggio..." : "Salva" }}</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useRecipesStore } from "@/stores/recipes.js"

const router = useRouter()
const store = useRecipesStore()

const form = ref({
  title: "",
  description: "",
  prep_time: null,
  cook_time: null,
  servings: null,
  difficulty: "",
  instructions: "",
  image_url: "",
})

const submit = async () => {
  try {
    const recipe = await store.createRecipe(form.value)
    router.push(`/recipes/${recipe.slug}`)
  } catch (e) {
    alert("Errore: " + e.message)
  }
}
</script>

<style scoped>
.create-form { max-width: 600px; margin: 0 auto; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.25rem; font-weight: 500; }
.form-group input, .form-group textarea { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.buttons { display: flex; gap: 1rem; justify-content: flex-end; }
button { padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; }
button[type="submit"] { background: #e65100; color: white; border: none; }
button[type="button"] { background: white; border: 1px solid #ddd; }
</style>
