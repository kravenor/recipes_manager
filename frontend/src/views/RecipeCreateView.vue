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

        <div class="form-group">
          <label>Difficoltà</label>
          <select v-model="form.difficulty">
            <option value="">Seleziona...</option>
            <option value="easy">Facile</option>
            <option value="medium">Media</option>
            <option value="hard">Difficile</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <IngredientSelector v-model="form.ingredients" />
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
import IngredientSelector from "@/components/IngredientSelector.vue"

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
  ingredients: [],
})

const submit = async () => {
  try {
    // Format ingredients for API
    const payload = {
      ...form.value,
      ingredients: form.value.ingredients.map(ing => ({
        id: ing.id,
        quantity: ing.quantity ? parseFloat(ing.quantity) : null,
        unit: ing.unit || null,
        notes: ing.notes || null,
      }))
    }

    const recipe = await store.createRecipe(payload)
    router.push(`/recipes/${recipe.slug}`)
  } catch (e) {
    alert("Errore: " + e.message)
  }
}
</script>

<style scoped>
.create-form { max-width: 800px; margin: 0 auto; }
.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333; }
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
.form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: #e65100; outline: none; }
.form-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
@media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
.buttons { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; }
button { padding: 0.75rem 1.5rem; border-radius: 4px; cursor: pointer; font-size: 1rem; }
button[type="submit"] { background: #e65100; color: white; border: none; }
button[type="submit"]:hover { background: #d84315; }
button[type="submit"]:disabled { background: #ccc; cursor: not-allowed; }
button[type="button"] { background: white; border: 1px solid #ddd; color: #666; }
button[type="button"]:hover { background: #f5f5f5; }
h1 { margin-bottom: 2rem; color: #333; }
</style>
