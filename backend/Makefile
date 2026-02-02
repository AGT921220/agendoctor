# Agendoctor - Makefile (ejecutar desde backend/ o raíz del proyecto)
ROOT            := $(shell dirname $(realpath $(firstword $(MAKEFILE_LIST))))/..
COMPOSE         := docker compose -f $(ROOT)/docker-compose.yml --project-directory $(ROOT)
PHP             := php-agendoctor
NGINX           := nginx-agendoctor
MYSQL           := mysql-agendoctor
DB_USER         ?= agendoctor
DB_PASS         ?= secret
DB_NAME         ?= agendoctor

.PHONY: up down install enter nginx migrate migrate-fresh clear test setup

up:
	$(COMPOSE) up -d --build

down:
	$(COMPOSE) down

install:
	docker exec $(PHP) composer install --no-interaction

enter:
	docker exec -it $(PHP) /bin/bash

nginx:
	docker exec -it $(NGINX) /bin/sh

migrate:
	docker exec $(PHP) php artisan migrate --force

migrate-fresh:
	docker exec $(PHP) php artisan migrate:fresh --force

setup: up
	@echo "Esperando a que MySQL esté listo..."
	@sleep 5
	$(MAKE) install
	$(MAKE) migrate
	@echo "✅ Backend listo. API: http://localhost:$${APP_PORT:-8080}/api/v1/health"

clear:
	docker exec $(PHP) /bin/bash -c "php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear"

test:
	docker exec $(PHP) ./vendor/bin/phpunit --stop-on-error --stop-on-failure

migrate-testing:
	docker exec $(PHP) php artisan migrate --env=testing --force

restart-database-testing:
	docker exec -i $(MYSQL) mysql -u $(DB_USER) -p$(DB_PASS) -e "DROP DATABASE IF EXISTS testing; CREATE DATABASE testing;"
	@[ -f storage/app/testing_db.sql ] && docker exec -i $(MYSQL) mysql -u $(DB_USER) -p$(DB_PASS) testing < storage/app/testing_db.sql || true

db-export:
	docker exec -i $(MYSQL) mysqldump -u $(DB_USER) -p$(DB_PASS) $(DB_NAME) > storage/app/agendoctor.sql
	@echo "✅ Exportado a storage/app/agendoctor.sql"

fpm-reload:
	docker exec -it $(PHP) /bin/sh -c 'kill -USR2 1 && echo "♻️ php-fpm reloaded"'
