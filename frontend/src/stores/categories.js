import { defineStore } from "pinia"
import apiClient from "@/api/client.js"

export const useCategoriesStore = defineStore("categories", {
  state: () => ({ categories: [], loading: false, error: null }),
  actions: {
    async fetchCategories() {
      this.loading = true
      try {
        const response = await apiClient.get("/categories")
        this.categories = response.data
      } catch (err) {
        this.error = err.message
      } finally {
        this.loading = false
      }
    },
  },
})
