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
/* Base 18px: tutti i rem delle viste scalano, testi leggibili su tablet a distanza */
html { font-size: 18px; -webkit-text-size-adjust: 100%; }
body { font-family: sans-serif; background: #f5f5f5; }
.navbar { position: sticky; top: 0; z-index: 200; display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 2rem; background: #fff; border-bottom: 1px solid #e0e0e0; }
.nav-brand a { font-size: 1.5rem; font-weight: bold; color: #e65100; text-decoration: none; }
.nav-links { display: flex; gap: 0.5rem; align-items: center; }
.nav-links a { color: #666; text-decoration: none; padding: 0.75rem 1rem; border-radius: 8px; }
.nav-links a:hover, .nav-links a.router-link-exact-active { color: #e65100; background: #fff3e0; }
main { padding: 2rem; max-width: 1200px; margin: 0 auto; }

@media (max-width: 1024px) {
  .navbar { padding: 0.5rem 1rem; }
  main { padding: 1.25rem 1rem; }
}

/* Dispositivi touch: target da almeno 48px e niente zoom/doppio tap */
@media (pointer: coarse) {
  a, button, input, select, textarea, [role="button"] { touch-action: manipulation; }
  button, input:not([type="checkbox"]):not([type="radio"]), select, .btn-primary, .btn-secondary { min-height: 48px; font-size: 1rem; }
  select[multiple] { min-height: 10rem; }
  /* Il suggerimento Ctrl/Cmd non serve: su touch la multi-selezione è nativa */
  .help-text { display: none; }
}
</style>
