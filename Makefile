install:
	cp api/.env.example api/.env
	docker compose build --no-cache --pull
	docker compose up -d
	docker compose exec php composer install
	docker compose exec php bin/console d:f:l -n
	docker compose exec php bin/console lexik:jwt:generate-keypair --overwrite

start:
	docker compose up -d
	docker compose exec php bin/console d:m:m -n

down:
	docker compose down

ci:
	cp api/.env.example api/.env
	docker compose build --no-cache --pull
	docker compose up -d
	docker compose exec php composer install
