import { defineStore } from "pinia"
import apiClient from "@/api/client.js"

export const useRecipesStore = defineStore("recipes", {
  state: () => ({
    recipes: [],
    currentRecipe: null,
    loading: false,
    error: null,
    pagination: { current_page: 1, last_page: 1, total: 0 },
  }),
  actions: {
    async fetchRecipes(params = {}) {
      this.loading = true
      try {
        const response = await apiClient.get("/recipes", { params })
        this.recipes = response.data.data || response.data
        this.pagination = response.data.meta || {}
      } catch (err) {
        this.error = err.message
      } finally {
        this.loading = false
      }
    },
    async fetchRecipe(slug) {
      this.loading = true
      try {
        const response = await apiClient.get(`/recipes/${slug}`)
        this.currentRecipe = response.data
      } catch (err) {
        this.error = err.message
      } finally {
        this.loading = false
      }
    },
    async searchRecipes(query, params = {}) {
      this.loading = true
      try {
        const response = await apiClient.get("/recipes/search", { params: { q: query, ...params } })
        this.recipes = response.data.data || response.data
      } catch (err) {
        this.error = err.message
      } finally {
        this.loading = false
      }
    },
    async createRecipe(data) {
      this.loading = true
      try {
        const response = await apiClient.post("/recipes", data)
        return response.data
      } catch (err) {
        this.error = err.message
        throw err
      } finally {
        this.loading = false
      }
    },
  },
})
