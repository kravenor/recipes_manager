<template>
  <div class="ingredient-selector">
    <label>Ingredienti</label>

    <div class="selected-ingredients">
      <div
        v-for="(ing, index) in selectedIngredients"
        :key="index"
        class="ingredient-row"
      >
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
        <button
          type="button"
          @click="removeIngredient(index)"
          class="remove-btn"
        >
          ×
        </button>
      </div>
    </div>

    <div class="ingredient-search" ref="searchContainer">
      <input
        v-model="searchQuery"
        @input="debouncedSearch"
        @focus="onSearchFocus"
        @keydown.esc="clearResults"
        @keydown.enter="addFreeIngredient"
        type="text"
        placeholder="Cerca ingrediente..."
        class="search-input"
        autocomplete="off"
      />
      <div
        v-if="searchQuery.length >= 2 && !loading && searchResults.length === 0"
        class="no-results"
      >
        Premi <kbd>Invio</kbd> per aggiungere "{{ searchQuery }}"
      </div>

      <ul v-if="searchResults.length > 0" class="search-results">
        <li
          v-for="ingredient in searchResults"
          :key="ingredient.id"
          @click.stop="selectIngredient(ingredient)"
          class="result-item"
          tabindex="0"
          role="button"
        >
          <span class="ingredient-name-result">{{ ingredient.name }}</span>
          <span v-if="ingredient.category" class="category">{{
            ingredient.category
          }}</span>
        </li>
      </ul>

      <div v-if="loading" class="loading">Caricamento...</div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from "vue";
import { useIngredientsStore } from "@/stores/ingredients.js";

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(["update:modelValue"]);

const ingredientsStore = useIngredientsStore();
const searchQuery = ref("");
const searchResults = ref([]);
const selectedIngredients = ref([...props.modelValue]);
const loading = ref(false);
const searchContainer = ref(null);
let debounceTimeout = null;

// Close results when clicking outside
const handleClickOutside = (event) => {
  if (searchContainer.value && !searchContainer.value.contains(event.target)) {
    searchResults.value = [];
  }
};

watch(
  selectedIngredients,
  (newVal) => {
    emit("update:modelValue", newVal);
  },
  { deep: true },
);

watch(
  () => props.modelValue,
  (newVal) => {
    selectedIngredients.value = [...newVal];
  },
);

const debouncedSearch = () => {
  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(async () => {
    if (searchQuery.value.length >= 2) {
      loading.value = true;
      await ingredientsStore.searchIngredients(searchQuery.value);
      searchResults.value = ingredientsStore.ingredients;
      loading.value = false;

      // Debug: vedi cosa arriva davvero
      console.log("Results:", searchResults.value);
    } else {
      searchResults.value = [];
    }
  }, 300);
};

const onSearchFocus = () => {
  // If there's already text and no results, trigger search
  if (searchQuery.value.length >= 2 && searchResults.value.length === 0) {
    debouncedSearch();
  }
};

const clearResults = () => {
  searchResults.value = [];
};

const selectIngredient = (ingredient) => {
  console.log("Selecting ingredient:", ingredient);
  addIngredient(ingredient);
};

const addIngredient = (ingredient) => {
  console.log("Adding ingredient:", ingredient);
  // Check if already added
  const exists = selectedIngredients.value.find(
    (ing) => ing.id === ingredient.id,
  );
  if (!exists) {
    selectedIngredients.value.push({
      id: ingredient.id,
      name: ingredient.name,
      quantity: null,
      unit: "",
      notes: "",
    });
    // Force update
    emit("update:modelValue", [...selectedIngredients.value]);
  }
  searchQuery.value = "";
  searchResults.value = [];
};

const removeIngredient = (index) => {
  selectedIngredients.value.splice(index, 1);
};

const addFreeIngredient = () => {
  const name = searchQuery.value.trim()
  if (!name || searchResults.value.length > 0) return
  addIngredient({ id: `local-${Date.now()}`, name })
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
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.result-item {
  padding: 0.75rem 0.5rem;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #eee;
  transition: background-color 0.1s;
  user-select: none;
}

.result-item:last-child {
  border-bottom: none;
}

.result-item:hover,
.result-item:focus {
  background-color: #fff3e0;
  outline: none;
}

.result-item:active {
  background-color: #ffe0b2;
}

.ingredient-name-result {
  flex: 1;
  font-weight: 500;
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


.no-results {
  padding: 0.5rem;
  color: #666;
  font-size: 0.875rem;
  border: 1px solid #ddd;
  border-top: none;
  border-radius: 0 0 4px 4px;
  background: #fffde7;
}
kbd {
  background: #eee;
  border: 1px solid #ccc;
  border-radius: 3px;
  padding: 0 4px;
  font-size: 0.8rem;
}
</style>
