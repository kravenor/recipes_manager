.PHONY: help up down restart build install migrate seed backend-install frontend-install test

help:
	@echo "Ricette App - Comandi disponibili:"
	@echo "  make up              - Avvia tutti i servizi Docker"
	@echo "  make down            - Ferma tutti i servizi"
	@echo "  make restart         - Riavvia i servizi"
	@echo "  make build           - Ricostruisce i container"
	@echo "  make install         - Installa dipendenze backend e frontend"
	@echo "  make migrate         - Esegue le migrazioni Laravel"
	@echo "  make seed            - Popola il database con dati di test"
	@echo "  make fresh           - Refresh completo database + seed"
	@echo "  make backend-install - Installa solo dipendenze Laravel"
	@echo "  make frontend-install- Installa solo dipendenze Vue"
	@echo "  make logs            - Mostra i log dei servizi"
	@echo "  make shell-back      - Entra nel container backend"
	@echo "  make shell-front     - Entra nel container frontend"
	@echo "  make es-import       - Importa dati in Elasticsearch"

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose restart

build:
	docker-compose build --no-cache

install: backend-install frontend-install

backend-install:
	docker-compose run --rm backend composer install
	-docker-compose run --rm backend php artisan key:generate

frontend-install:
	cd frontend && npm install

migrate:
	docker-compose exec backend php artisan migrate

seed:
	docker-compose exec backend php artisan db:seed

fresh:
	docker-compose exec backend php artisan migrate:fresh --seed

logs:
	docker-compose logs -f

shell-back:
	docker-compose exec backend bash

shell-front:
	docker-compose exec frontend sh

es-import:
	docker-compose exec backend php artisan scout:import "App\\Models\\Recipe"
	docker-compose exec backend php artisan scout:import "App\\Models\\Ingredient"