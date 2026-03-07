start:
	docker compose up -d

shut-down:
	docker compose down

ssh:
	docker compose exec web bash

db:
	docker compose exec db bash -c "PGPASSWORD=symfony psql -U symfony -d symfony"

xdebug:
	docker compose -f docker-compose.yml -f docker-compose.xdebug.yml up -d --build

recreat:
	docker compose up -d --build --force-recreate

composer-install:
	docker compose exec web composer install

migrate-all:
	docker compose exec web bin/console doctrine:migrations:migrate