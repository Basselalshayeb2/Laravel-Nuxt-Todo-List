.PHONY: setup up down logs test test-backend test-frontend test-e2e lint analyse docs fresh

setup:
	docker compose build
	cd backend && composer install
	cd frontend && pnpm install --frozen-lockfile

up:
	docker compose up --build -d

down:
	docker compose down

logs:
	docker compose logs -f

test: test-backend test-frontend

test-backend:
	cd backend && php artisan test

test-frontend:
	cd frontend && pnpm test

test-e2e:
	cd frontend && pnpm test:e2e

lint:
	cd backend && vendor/bin/pint --test
	cd frontend && pnpm lint

analyse:
	cd backend && vendor/bin/phpstan analyse --memory-limit=1G
	cd frontend && pnpm typecheck

docs:
	cd backend && php artisan scribe:generate

fresh:
	docker compose exec api php artisan migrate:fresh --seed --force
