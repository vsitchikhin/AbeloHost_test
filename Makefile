# Makefile for PHP Blog Project

.PHONY: up down build install test lint e2e seed shell assets assets-prod dev-assets type-check help

help:
	@echo "Available commands:"
	@echo "  make up           - Start containers"
	@echo "  make down         - Stop containers"
	@echo "  make build        - Build and start containers"
	@echo "  make install      - Install PHP and JS dependencies"
	@echo "  make test         - Run unit tests"
	@echo "  make lint         - Run all linters and static analysis"
	@echo "  make format       - Auto-format code using Prettier"
	@echo "  make e2e          - Run end-to-end tests"
	@echo "  make seed         - Seed the database with fake data"
	@echo "  make shell        - Enter the app container shell"
	@echo "  make assets       - Build frontend assets (dev)"
	@echo "  make assets-prod  - Build frontend assets (production, minified)"
	@echo "  make dev-assets   - Build and watch frontend assets"
	@echo "  make type-check   - Run TypeScript type checker"

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose up -d --build

install:
	composer install
	npm install

test:
	docker-compose exec app vendor/bin/phpunit

lint:
	docker-compose exec app vendor/bin/phpcs
	docker-compose exec app vendor/bin/phpstan analyze
	npm run lint

e2e:
	npm run test:e2e

seed:
	docker-compose exec app php database/seed.php

shell:
	docker-compose exec app bash

format:
	npm run format

assets:
	npm run build

assets-prod:
	npm run build:prod

dev-assets:
	npm run dev

type-check:
	npm run type-check
