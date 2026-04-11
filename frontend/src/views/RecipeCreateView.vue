<template>
  <div class="recipe-create">
    <h1>Nuova Ricetta</h1>

    <form @submit.prevent="submit" class="recipe-form">
      <div class="form-group">
        <label for="title">Titolo *</label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          required
          placeholder="Es. Pasta al pomodoro"
        />
      </div>

      <div class="form-group">
        <label for="description">Descrizione</label>
        <textarea
          id="description"
          v-model="form.description"
          rows="3"
          placeholder="Breve descrizione della ricetta"
        ></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="prep_time">Tempo prep (min)</label>
          <input
            id="prep_time"
            v-model.number="form.prep_time"
            type="number"
            min="0"
            placeholder="15"
          />
        </div>

        <div class="form-group">
          <label for="cook_time">Tempo cottura (min)</label>
          <input
            id="cook_time"
            v-model.number="form.cook_time"
            type="number"
            min="0"
            placeholder="20"
          />
        </div>

        <div class="form-group">
          <label for="servings">Porzioni</label>
          <input
            id="servings"
            v-model.number="form.servings"
            type="number"
            min="1"
            placeholder="4"
          />
        </div>
      </div>

      <div class="form-group">
        <label for="difficulty">Difficoltà</label>
        <select id="difficulty" v-model="form.difficulty">
          <option value="">Seleziona...</option>
          <option value="easy">Facile</option>
          <option value="medium">Media</option>
          <option value="hard">Difficile</option>
        </select>
      </div>

      <IngredientSelector v-model="form.ingredients" />

      <div class="form-group">
        <label for="instructions">Istruzioni *</label>
        <textarea
          id="instructions"
          v-model="form.instructions"
          rows="6"
          required
          placeholder="Descrivi i passaggi della ricetta..."
        ></textarea>
      </div>

      <div v-if="error" class="error-message">
        {{ error }}
      </div>

      <div class="form-actions">
        <button type="button" @click="$router.back()" class="btn-secondary">
          Annulla
        </button>
        <button type="submit" :disabled="saving" class="btn-primary">
          {{ saving ? "Salvataggio..." : "Salva Ricetta" }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useRecipesStore } from "@/stores/recipes.js"
import { useIngredientsStore } from "@/stores/ingredients.js"
import IngredientSelector from "@/components/IngredientSelector.vue"

const router = useRouter()
const recipesStore = useRecipesStore()
const ingredientsStore = useIngredientsStore()

const form = ref({
  title: "",
  description: "",
  instructions: "",
  prep_time: null,
  cook_time: null,
  servings: null,
  difficulty: "",
  ingredients: [],
})

const saving = ref(false)
const error = ref("")

const submit = async () => {
  error.value = ""

  // Validation
  if (!form.value.title.trim()) {
    error.value = "Inserisci un titolo per la ricetta"
    return
  }

  if (!form.value.instructions.trim()) {
    error.value = "Inserisci le istruzioni per la ricetta"
    return
  }

  saving.value = true

  try {
    // Separate new ingredients (local- IDs) from existing ones
    const existingIngredients = []
    const newIngredients = []

    for (const ing of form.value.ingredients) {
      if (!ing.id) continue

      if (String(ing.id).startsWith("local-")) {
        // This is a new ingredient - needs to be created
        newIngredients.push(ing)
      } else {
        // Existing ingredient
        existingIngredients.push({
          id: parseInt(ing.id, 10),
          quantity: ing.quantity ? parseFloat(ing.quantity) : null,
          unit: ing.unit || null,
          notes: ing.notes || null,
        })
      }
    }

    // Create new ingredients and get their real IDs
    const createdIngredients = []
    for (const ing of newIngredients) {
      try {
        const created = await ingredientsStore.findOrCreateIngredient(
          ing.name,
          ing.category || null
        )
        createdIngredients.push({
          id: parseInt(created.id, 10),
          quantity: ing.quantity ? parseFloat(ing.quantity) : null,
          unit: ing.unit || null,
          notes: ing.notes || null,
        })
      } catch (err) {
        console.error(`Failed to create ingredient "${ing.name}":`, err)
        error.value = `Errore durante la creazione dell'ingrediente "${ing.name}"`
        saving.value = false
        return
      }
    }

    // Prepare payload with all ingredients (existing + newly created)
    const payload = {
      ...form.value,
      ingredients: [...existingIngredients, ...createdIngredients],
    }

    const result = await recipesStore.createRecipe(payload)
    router.push(`/recipes/${result.slug}`)
  } catch (err) {
    console.error("Failed to create recipe:", err)
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = errors.join(", ")
    } else {
      error.value = "Errore durante il salvataggio. Riprova."
    }
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.recipe-create {
  max-width: 800px;
  margin: 0 auto;
}

h1 {
  margin-bottom: 1.5rem;
  color: #333;
}

.recipe-form {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

@media (max-width: 600px) {
  .form-row {
    grid-template-columns: 1fr;
  }
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #555;
}

input,
textarea,
select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  box-sizing: border-box;
}

input:focus,
textarea:focus,
select:focus {
  outline: none;
  border-color: #e65100;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.btn-primary {
  padding: 0.75rem 1.5rem;
  background: #e65100;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.btn-primary:hover:not(:disabled) {
  background: #d84315;
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-secondary {
  padding: 0.75rem 1.5rem;
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  color: #666;
}

.btn-secondary:hover {
  background: #f5f5f5;
}

.error-message {
  background: #ffebee;
  color: #c62828;
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
}
</style>
