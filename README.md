# Ricette - Gestione Ricette Culinarie

Applicazione web per la gestione di ricette culinarie con ricerca avanzata per ingredienti.

## Architettura

- **Backend**: Laravel 11 API RESTful
- **Frontend**: Vue 3 + Vite + Pinia
- **Database**: MySQL 8.0
- **Ricerca**: Elasticsearch 8.x
- **Deploy**: Docker Compose

## Struttura Directory

```
ricette-app/
├── backend/                 # Laravel API
│   ├── app/
│   ├── database/
│   ├── docker/
│   └── ...
├── frontend/               # Vue 3 SPA
│   ├── src/
│   ├── public/
│   └── ...
├── docker-compose.yml      # Orchestrazione completa
├── Makefile               # Comandi utili
└── README.md
```

## Requisiti

- Docker e Docker Compose
- Node.js 20+ (per sviluppo frontend)
- Composer (per sviluppo backend)

## Avvio Rapido

### 1. Clona il repository

```bash
git clone <repository-url>
cd ricette-app
```

### 2. Avvia i servizi con Docker

```bash
make up
# oppure
docker-compose up -d
```

### 3. Installa le dipendenze

```bash
# Backend
cd backend && composer install

# Frontend
cd ../frontend && npm install
```

### 4. Configura il database

```bash
# Esegui le migrazioni
docker-compose exec backend php artisan migrate

# (Opzionale) Popola con dati di test
docker-compose exec backend php artisan db:seed
```

### 5. Importa dati in Elasticsearch

```bash
docker-compose exec backend php artisan scout:import "App\\Models\\Recipe"
docker-compose exec backend php artisan scout:import "App\\Models\\Ingredient"
```

### 6. Accedi all'applicazione

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api
- **Elasticsearch**: http://localhost:9200
- **Kibana** (opzionale): http://localhost:5601

## API Endpoints

### Ricette

- `GET /api/recipes` - Lista ricette (paginate)
- `GET /api/recipes/{slug}` - Dettaglio ricetta
- `POST /api/recipes` - Crea nuova ricetta
- `PUT /api/recipes/{id}` - Aggiorna ricetta
- `DELETE /api/recipes/{id}` - Elimina ricetta
- `GET /api/recipes/search?q={query}` - Ricerca fulltext

### Ingredienti

- `GET /api/ingredients` - Lista ingredienti
- `GET /api/ingredients/search?q={query}` - Ricerca ingredienti

### Categorie

- `GET /api/categories` - Lista categorie

## Comandi Utili

```bash
# Avvia i servizi
make up

# Ferma i servizi
make down

# Riavvia i servizi
make restart

# Esegui migrazioni
make migrate

# Popola database con dati di test
make seed

# Refresh completo database
make fresh

# Importa dati in Elasticsearch
make es-import

# Log dei servizi
make logs

# Entra nel container backend
make shell-back

# Entra nel container frontend
make shell-front
```

## Database Schema

### Tabelle

- **recipes**: Ricette culinarie
- **ingredients**: Ingredienti
- **categories**: Categorie
- **tags**: Tag
- **recipe_ingredient**: Pivot ricetta-ingrediente
- **recipe_category**: Pivot ricetta-categoria
- **recipe_tag**: Pivot ricetta-tag
- **users**: Utenti (per futura autenticazione)

### Ricerca

Elasticsearch indicizza:
- **recipes**: title, description, instructions, ingredients
- **ingredients**: name, category

## Sviluppo

### Frontend

```bash
cd frontend
npm run dev        # Avvio server sviluppo
npm run build      # Build produzione
npm run preview    # Preview build
```

### Backend

```bash
cd backend
php artisan serve  # Avvio server sviluppo
php artisan test   # Esegui test
```

## Configurazione

### Variabili Ambiente

Copia `.env.example` in `.env` e modifica:

```env
# Database
MYSQL_DATABASE=ricette
MYSQL_USER=ricette
MYSQL_PASSWORD=ricette

# Porte
BACKEND_PORT=8000
FRONTEND_PORT=5173
MYSQL_PORT=3306
ES_PORT=9200
KIBANA_PORT=5601
```

## Licenza

MIT License
