import { createRouter, createWebHistory } from "vue-router"
import HomeView from "@/views/HomeView.vue"

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", name: "home", component: HomeView },
    { path: "/recipes", name: "recipes", component: () => import("@/views/RecipeListView.vue") },
    { path: "/recipes/new", name: "recipe-new", component: () => import("@/views/RecipeCreateView.vue") },
    { path: "/recipes/:slug", name: "recipe-detail", component: () => import("@/views/RecipeDetailView.vue") },
    { path: "/search", name: "search", component: () => import("@/views/SearchResultsView.vue") },
  ],
})

export default router
