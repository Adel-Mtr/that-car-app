.PHONY: setup demo dev test build docker-up docker-down reset-demo

setup:
	composer install
	@test -f .env || cp .env.example .env
	php artisan key:generate
	@mkdir -p database && touch database/database.sqlite
	php artisan migrate --force
	npm ci
	npm run build

demo:
	php artisan migrate:fresh --seed --force

dev:
	composer dev

test:
	php artisan test --compact
	vendor/bin/pint --test
	npm run build

build:
	npm run build

docker-up:
	docker compose up --build

docker-down:
	docker compose down

reset-demo:
	docker compose down -v
	docker compose up --build
