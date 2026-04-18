# CLAUDE.md - Contesto AI per il Progetto Ricette

Questo file fornisce il contesto necessario agli assistenti AI per lavorare efficacemente su questo progetto.

---

## Panoramica del Progetto

**Ricette** è un'applicazione web per la gestione e la ricerca di ricette culinarie, con focus sulla ricerca per ingredienti multipli.

### Stack Tecnologico

| Componente | Tecnologia | Versione |
|------------|------------|----------|
| Backend | Laravel | 12.x |
| Frontend | Vue.js | 3.4+ |
| Build Tool | Vite | 8.x |
| State Management | Pinia | 2.x |
| Database | MySQL | 8.0 |
| Ricerca | Elasticsearch | 8.x |
| Container | Docker Compose | - |

---

## Architettura

### Backend (`/backend`)

- **Framework**: Laravel 12 con struttura API RESTful
- **Pattern**: MVC con Resources per API
- **Ricerca**: Laravel Scout con driver Elasticsearch
- **Modelli principali**:
  - `Recipe` - Ricette culinarie
  - `Ingredient` - Ingredienti con possibile categoria
  - `Category` - Categorie di ricette
  - `Tag` - Tag per ricette
- **Controller API**:
  - `RecipeController` - CRUD ricette + ricerca
  - `IngredientController` - Gestione ingredienti
  - `CategoryController` - Gestione categorie

### Frontend (`/frontend`)

- **Framework**: Vue 3 Composition API
- **Router**: Vue Router 4
- **State**: Pinia stores modulari
- **HTTP Client**: Axios
- **Viste principali**:
  - `HomeView` - Homepage con ricerca testuale
  - `RecipeListView` - Lista ricette
  - `RecipeDetailView` - Dettaglio ricetta
  - `RecipeCreateView` - Creazione nuova ricetta
  - `RecipeSearchByIngredientsView` - Ricerca per ingredienti multipli
  - `SearchResultsView` - Risultati ricerca testuale
- **Componenti**:
  - `IngredientSelector` - Selezione multipla ingredienti con autocomplete

---

## Convenzioni di Codice

### Backend (PHP/Laravel)

- PSR-12 coding standard
- Nomi in inglese per classi, metodi e variabili
- Relazioni many-to-many con tabelle pivot esplicite
- API responses sempre con Resources (formato `data` + `meta`)
- Query con eager loading per evitare N+1

### Frontend (JavaScript/Vue)

- Vue 3 Composition API con `<script setup>`
- Stores Pinia modulari (uno per dominio)
- Componenti riutilizzabili in `/components`
- Views in `/views`
- API client centralizzato in `@/api/client.js`
- Stili scoped per componenti

---

## Flussi Principali

### 1. Ricerca per Ingredienti

```
Utente seleziona ingredienti → IngredientSelector
↓
Frontend chiama GET /api/recipes?ingredients[]=pomodoro&ingredients[]=basilico
↓
Backend filtra ricette che contengono TUTTI gli ingredienti (AND logico)
↓
Frontend mostra ricette con evidenziazione ingredienti matchati
```

### 2. Ricerca Testuale (Full-text)

```
Utente digita testo → Scout/Elasticsearch
↓
GET /api/recipes/search?q={query}
↓
Ricerca in: title, description, instructions, ingredients
```

### 3. Creazione Ricetta

```
Form multi-step con IngredientSelector
↓
POST /api/recipes con ingredients[], category_ids[], tag_ids[]
↓
Backend crea ricetta + relazioni many-to-many
↓
Reindirizza a dettaglio ricetta
```

---

## Endpoint API Chiave

| Metodo | Endpoint | Descrizione |
|--------|----------|-------------|
| GET | `/api/recipes` | Lista paginata, filtri: `category`, `ingredients[]`, `difficulty` |
| GET | `/api/recipes/search?q=` | Ricerca full-text Elasticsearch |
| GET | `/api/recipes/{slug}` | Dettaglio ricetta |
| POST | `/api/recipes` | Crea ricetta (auth opzionale) |
| GET | `/api/ingredients` | Lista ingredienti |
| GET | `/api/ingredients/search?q=` | Cerca ingredienti |
| POST | `/api/ingredients` | Crea nuovo ingrediente |

---

## Database Schema

```
recipes
├── id, title, slug, description, instructions
├── prep_time, cook_time, servings, difficulty
├── image_url, user_id, timestamps

ingredients
├── id, name, category, timestamps

categories
├── id, name, slug, timestamps

tags
├── id, name, slug, timestamps

recipe_ingredient (pivot)
├── recipe_id, ingredient_id
├── quantity, unit, notes

recipe_category (pivot)
├── recipe_id, category_id

recipe_tag (pivot)
├── recipe_id, tag_id
```

---

## Sviluppo Locale

### Avvio Ambiente

```bash
# Tutti i servizi
make up

# Solo backend+database
docker-compose up -d mysql backend

# Frontend dev server
cd frontend && npm run dev
```

### Comandi Utili

```bash
# Migrazioni
docker-compose exec backend php artisan migrate

# Seed dati
docker-compose exec backend php artisan db:seed

# Importa in Elasticsearch
make es-import
# oppure
docker-compose exec backend php artisan scout:import "App\\Models\\Recipe"
docker-compose exec backend php artisan scout:import "App\\Models\\Ingredient"

# Formattazione codice
cd backend && ./vendor/bin/pint
cd frontend && npm run lint
```

---

## Pattern Comuni

### Aggiungere un nuovo filtro ricette

1. Modifica `RecipeController@index()` - aggiungi parametro request
2. Aggiungi condizione whereHas o where sulla query
3. Testa con curl o browser

### Aggiungere una nuova vista

1. Crea file in `frontend/src/views/`
2. Aggiungi route in `frontend/src/router/index.js`
3. Linka da altre viste o componenti

### Modificare ricerca Elasticsearch

1. Modifica `toSearchableArray()` nel modello
2. Re-importa: `php artisan scout:import "App\\Models\\Modello"`

---

## Note Importanti

- **Elasticsearch**: Richiede import manuale dopo cambiamenti ai dati
- **File Upload**: Non implementato - le immagini usano URL esterni
- **Autenticazione**: Presente ma opzionale per la maggior parte delle operazioni
- **CORS**: Configurato per permettere richieste dal frontend (porta 5173)

---

## Links Utili

- Frontend: http://localhost:5173
- API: http://localhost:8000/api
- Elasticsearch: http://localhost:9200
- Kibana: http://localhost:5601
