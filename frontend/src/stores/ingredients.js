import { defineStore } from "pinia"
import apiClient from "@/api/client.js"

export const useIngredientsStore = defineStore("ingredients", {
  state: () => ({ ingredients: [], loading: false, error: null }),
  actions: {
    async fetchIngredients(params = {}) {
      this.loading = true
      try {
        const response = await apiClient.get("/ingredients", { params })
        this.ingredients = response.data.data || response.data
      } catch (err) {
        this.error = err.message
      } finally {
        this.loading = false
      }
    },
    async searchIngredients(query) {
      this.loading = true
      try {
        const response = await apiClient.get("/ingredients/search", { params: { q: query } })
        this.ingredients = response.data.data || response.data
      } catch (err) {
        this.error = err.message
      } finally {
        this.loading = false
      }
    },
  },
})
