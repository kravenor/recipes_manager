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
    async createIngredient(name, category = null) {
      try {
        const response = await apiClient.post("/ingredients", {
          name,
          category,
        })
        return response.data
      } catch (err) {
        // If 422 (duplicate), try to find existing
        if (err.response?.status === 422) {
          const searchResponse = await apiClient.get("/ingredients/search", {
            params: { q: name },
          })
          const results = searchResponse.data.data || searchResponse.data
          const existing = results.find(
            (ing) => ing.name.toLowerCase() === name.toLowerCase()
          )
          if (existing) return existing
        }
        throw err
      }
    },
    async findOrCreateIngredient(name, category = null) {
      const response = await apiClient.post("/ingredients/find-or-create", {
        name,
        category,
      })
      return response.data
    },
  },
})
