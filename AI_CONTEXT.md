# AI Context - Recipe Manager Project

> File di contesto per assistenti AI che lavorano su questo progetto.

## In Breve

Applicazione full-stack per gestire e cercare ricette culinarie, con ricerca avanzata per ingredienti multipli.

**Tech Stack**: Laravel 12 (API) + Vue 3 (SPA) + MySQL + Elasticsearch

## Struttura

```
ricette/
├── backend/          # Laravel API REST
│   ├── app/Models/   # Recipe, Ingredient, Category, Tag
│   └── app/Http/Controllers/Api/
├── frontend/         # Vue 3 + Vite + Pinia
│   ├── src/views/    # Viste principali
│   ├── src/components/ # Componenti riutilizzabili
│   └── src/stores/   # Pinia stores
├── docker-compose.yml
├── Makefile
└── CLAUDE.md         # Documentazione dettagliata
```

## Comandi Essenziali

```bash
make up              # Avvia tutto con Docker
make migrate         # Esegui migrazioni
make es-import       # Importa in Elasticsearch
cd frontend && npm run dev   # Dev server frontend
```

## Pattern Chiave

- **Ricerca ingredienti**: `GET /api/recipes?ingredients[]=pomodoro&ingredients[]=basilico`
- **Ricerca testuale**: `GET /api/recipes/search?q=pasta` (Elasticsearch)
- **Relazioni**: Many-to-many con tabelle pivot

## Vedi Anche

- `CLAUDE.md` - Documentazione completa
- `README.md` - Istruzioni setup
- `Makefile` - Comandi utili
