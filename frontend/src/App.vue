<template>
  <div id="app">
    <nav class="navbar">
      <div class="nav-brand"><router-link to="/">🍳 Ricette</router-link></div>
      <div class="nav-links">
        <router-link to="/">Home</router-link>
        <router-link to="/recipes">Ricette</router-link>
        <router-link to="/recipes/new">+ Nuova</router-link>
      </div>
    </nav>
    <main>
      <router-view />
    </main>
  </div>
</template>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: sans-serif; background: #f5f5f5; }
.navbar { display: flex; justify-content: space-between; padding: 1rem 2rem; background: #fff; border-bottom: 1px solid #e0e0e0; }
.nav-brand a { font-size: 1.5rem; font-weight: bold; color: #e65100; text-decoration: none; }
.nav-links { display: flex; gap: 1.5rem; align-items: center; }
.nav-links a { color: #666; text-decoration: none; }
.nav-links a:hover { color: #e65100; }
main { padding: 2rem; max-width: 1200px; margin: 0 auto; }
</style>
