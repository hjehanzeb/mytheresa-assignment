DOCKER_COMPOSE = docker-compose
EXEC_PHP = $(DOCKER_COMPOSE) exec -T php
EXEC_NPM = $(DOCKER_COMPOSE) exec -T php npm
EXEC_YARN = $(DOCKER_COMPOSE) exec -T php yarn
EXEC_GULP = $(DOCKER_COMPOSE) exec -T php gulp
EXEC_SYMFONY = $(DOCKER_COMPOSE) exec -T php bin/console
EXEC_DB = $(DOCKER_COMPOSE) exec -T database sh -c
QUALITY_ASSURANCE = $(DOCKER_COMPOSE) run --rm quality-assurance
COMPOSER = $(EXEC_PHP) composer

.DEFAULT_GOAL := help

help: ## This help dialog.
	@echo "${GREEN}Skeleton${RESET}"
	@awk '/^[a-zA-Z\-\_0-9]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  ${YELLOW}%-$(TARGET_MAX_CHAR_NUM)s${RESET} ${GREEN}%s${RESET}\n", helpCommand, helpMessage; \
		} \
		isTopic = match(lastLine, /^###/); \
	    if (isTopic) { printf "\n%s\n", $$1; } \
	} { lastLine = $$0 }' $(MAKEFILE_LIST)

#################################
Docker:

pull: docker-compose.yml
	@echo "\nPulling local images...\e[0m"
	@$(DOCKER_COMPOSE) pull --quiet

build: docker-compose.yml pull ##Build docker
	@echo "\nBuilding local images...\e[0m"
	@$(DOCKER_COMPOSE) build

## Up environment
up: docker-compose.yml ##Up docker
	@$(DOCKER_COMPOSE) up -d --remove-orphans

## Up environment with rebuild
up-recreate: docker-compose.yml ##Up docker
	@$(DOCKER_COMPOSE) up -d --build --force-recreate --remove-orphans

## Down environment
down: docker-compose.yml ##Down docker
	@$(DOCKER_COMPOSE) kill
	@$(DOCKER_COMPOSE) down --remove-orphans

## View output from all containers
logs: docker-compose.yml ##Logs from docker
	@${DOCKER_COMPOSE} logs -f --tail 0

.PHONY: pull build up down logs

#################################
Project:

## Up the project and load database
install: build up vendor db-create db-migrate

## Reset the project
reset: down install

## Start containers (unpause)
start: docker-compose.yml
	@$(DOCKER_COMPOSE) unpause || true
	@$(DOCKER_COMPOSE) start || true

##Stop containers (pause)
stop: docker-compose.yml
	@$(DOCKER_COMPOSE) pause || true

##Install composer
vendor: #mytheresa/composer.lock
	@echo "\nInstalling composer packages...\e[0m"
	@$(COMPOSER) install

## Update composer
composer-update: #mytheresa/composer.json
	@echo "\nUpdating composer packages...\e[0m"
	@$(COMPOSER) update


## Clear symfony cache
cc:
	@echo "\nClearing cache...\e[0m"
	@$(EXEC_SYMFONY) c:c
	@$(EXEC_SYMFONY) cache:pool:clear cache.global_clearer

wait-db:
	@echo "\nWaiting for DB...\e[0m"
	@$(EXEC_PHP) php -r "set_time_limit(60);for(;;){if(@fsockopen('database',3306))die;echo \"\";sleep(1);}"

#################################
Database:

## Create database
db-create: wait-db
	@echo "\nCreating database...\e[0m"
	@$(EXEC_SYMFONY) doctrine:database:create --if-not-exists

## Generate migration by diff
db-diff: wait-db
	$(EXEC_SYMFONY) doctrine:migration:diff --formatted --allow-empty-diff

## Load migration
db-migrate: wait-db
	@echo "\nRunning migrations...\e[0m"
	@$(EXEC_SYMFONY) doctrine:migration:migrate --no-interaction --all-or-nothing


#################################
Test:
run-tests: wait-db db-tests-create db-tests-migrate
	@echo "\nRunning Test...\e[0m"
	@$(EXEC_PHP) bin/phpunit 

## Create database
db-tests-create: wait-db
	@echo "\nCreating database...\e[0m"
	@$(EXEC_SYMFONY) --env=test doctrine:database:create --if-not-exists

## Load migration
db-tests-migrate: wait-db
	@echo "\nRunning migrations...\e[0m"
	@$(EXEC_SYMFONY) --env=test doctrine:migration:migrate --no-interaction --no-debug
