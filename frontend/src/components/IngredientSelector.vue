<template>
  <div class="ingredient-selector">
    <label>Ingredienti</label>

    <div class="selected-ingredients">
      <div v-for="(ing, index) in selectedIngredients" :key="index" class="ingredient-row">
        <span class="ingredient-name">{{ ing.name }}</span>
        <input
          v-model="ing.quantity"
          type="number"
          step="0.01"
          placeholder="Qtà"
          class="quantity-input"
        />
        <input
          v-model="ing.unit"
          type="text"
          placeholder="Unità (g, ml, etc.)"
          class="unit-input"
        />
        <input
          v-model="ing.notes"
          type="text"
          placeholder="Note"
          class="notes-input"
        />
        <button type="button" @click="removeIngredient(index)" class="remove-btn">×</button>
      </div>
    </div>

    <div class="ingredient-search">
      <input
        v-model="searchQuery"
        @input="debouncedSearch"
        type="text"
        placeholder="Cerca ingrediente..."
        class="search-input"
      />

      <ul v-if="searchResults.length > 0" class="search-results">
        <li
          v-for="ingredient in searchResults"
          :key="ingredient.id"
          @click="addIngredient(ingredient)"
          class="result-item"
        >
          {{ ingredient.name }}
          <span v-if="ingredient.category" class="category">{{ ingredient.category }}</span>
        </li>
      </ul>

      <div v-if="loading" class="loading">Caricamento...</div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue"
import { useIngredientsStore } from "@/stores/ingredients.js"

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(["update:modelValue"])

const ingredientsStore = useIngredientsStore()
const searchQuery = ref("")
const searchResults = ref([])
const selectedIngredients = ref([...props.modelValue])
const loading = ref(false)
let debounceTimeout = null

watch(selectedIngredients, (newVal) => {
  emit("update:modelValue", newVal)
}, { deep: true })

watch(() => props.modelValue, (newVal) => {
  selectedIngredients.value = [...newVal]
})

const debouncedSearch = () => {
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(async () => {
    if (searchQuery.value.length >= 2) {
      loading.value = true
      await ingredientsStore.searchIngredients(searchQuery.value)
      searchResults.value = ingredientsStore.ingredients
      loading.value = false
    } else {
      searchResults.value = []
    }
  }, 300)
}

const addIngredient = (ingredient) => {
  // Check if already added
  const exists = selectedIngredients.value.find(ing => ing.id === ingredient.id)
  if (!exists) {
    selectedIngredients.value.push({
      id: ingredient.id,
      name: ingredient.name,
      quantity: null,
      unit: "",
      notes: ""
    })
  }
  searchQuery.value = ""
  searchResults.value = []
}

const removeIngredient = (index) => {
  selectedIngredients.value.splice(index, 1)
}
</script>

<style scoped>
.ingredient-selector {
  margin-bottom: 1rem;
}

.selected-ingredients {
  margin-bottom: 1rem;
}

.ingredient-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  background: #f5f5f5;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.ingredient-name {
  flex: 1;
  font-weight: 500;
}

.quantity-input {
  width: 80px;
  padding: 0.25rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.unit-input {
  width: 100px;
  padding: 0.25rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.notes-input {
  flex: 2;
  padding: 0.25rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.remove-btn {
  background: #d32f2f;
  color: white;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.ingredient-search {
  position: relative;
}

.search-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.search-results {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #ddd;
  border-top: none;
  border-radius: 0 0 4px 4px;
  list-style: none;
  max-height: 200px;
  overflow-y: auto;
  z-index: 100;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.result-item {
  padding: 0.5rem;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.result-item:hover {
  background-color: #f5f5f5;
}

.category {
  font-size: 0.75rem;
  color: #666;
  background: #e3f2fd;
  padding: 0.125rem 0.5rem;
  border-radius: 4px;
}

.loading {
  padding: 0.5rem;
  color: #666;
  font-size: 0.875rem;
}
</style>
