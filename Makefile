HOST_GROUP_ID = $(shell id -g)
HOST_USER = ${USER}
HOST_UID = $(shell id -u)

export HOST_UID
export HOST_USER
export HOST_GROUP_ID

DOCKER_COMPOSE_DEV = docker compose

install:
	$(DOCKER_COMPOSE_DEV) build
	$(MAKE) composer-install
	$(MAKE) assets-install
	$(MAKE) start

composer-install:
	$(DOCKER_COMPOSE_DEV) run --rm php bash -ci 'php -d memory_limit=4G bin/composer install'

composer-update:
	$(DOCKER_COMPOSE_DEV) run --rm php bash -ci 'php -d memory_limit=4G bin/composer update -W'

start:
	$(DOCKER_COMPOSE_DEV) up -d

start-verbose:
	$(DOCKER_COMPOSE_DEV) up

stop:
	$(DOCKER_COMPOSE_DEV) down

connect:
	$(DOCKER_COMPOSE_DEV) exec php bash

clear:
	php ./bin/console cache:clear

migrations:
	$(DOCKER_COMPOSE_DEV) run --rm php bash -ci 'php -d memory_limit=4G ./bin/console doctrine:migrations:migrate --no-interaction'

FILE ?= dump.sql

db-dump:
	$(DOCKER_COMPOSE_DEV) exec database sh -c 'PGPASSWORD=$${POSTGRES_PASSWORD} pg_dump -U $${POSTGRES_USER} $${POSTGRES_DB}' > $(FILE)

db-restore:
	cat $(FILE) | $(DOCKER_COMPOSE_DEV) exec -T database sh -c 'PGPASSWORD=$${POSTGRES_PASSWORD} psql -U $${POSTGRES_USER} $${POSTGRES_DB}'

db-drop:
	$(DOCKER_COMPOSE_DEV) run --rm php bash -ci 'php -d memory_limit=4G ./bin/console doctrine:database:drop --force'

assets-install:
	$(DOCKER_COMPOSE_DEV) run --rm php bash -ci 'php -d memory_limit=4G ./bin/console importmap:install'

assets-compile:
	$(DOCKER_COMPOSE_DEV) run --rm php bash -ci 'php -d memory_limit=4G ./bin/console asset-map:compile'
