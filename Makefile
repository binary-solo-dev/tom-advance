.PHONY: help build up down restart install composer-install test

CLI=
# OS-specific overrides
ifeq ($(OS),Windows_NT)
	COMPOSE_FILE += -f docker-compose.windows.yaml
	CLI=winpty
else
	UNAME_S := $(shell uname -s)
	ifeq ($(UNAME_S),Linux)
		COMPOSE_FILE += -f docker-compose.linux.yaml
	endif
	ifeq ($(UNAME_S),Darwin)
		COMPOSE_FILE += -f docker-compose.macos.yaml
	endif
endif
DOCKER_COMPOSE = docker compose ${COMPOSE_FILE}

# Help command to display available commands
help: ## Display this help message
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@awk '/^[a-zA-Z\-\_0-9]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  \033[32m%-20s\033[0m %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

# Docker commands
build: ## Build the Docker containers
	@echo "Building Docker containers..."
	${DOCKER_COMPOSE} build

 up: ## Start the Docker containers
	@echo "Starting Docker containers..."
	${DOCKER_COMPOSE} up -d
	@echo "Application is now available at http://localhost:2977"

down: ## Stop the Docker containers
	@echo "Stopping Docker containers..."
	${DOCKER_COMPOSE} down

restart: down up ## Restart the Docker containers

shell: ## Access the PHP container shell
	@echo "🔐 Accessing PHP container shell..."
	${CLI} ${DOCKER_COMPOSE} exec tms sh

# Installation commands
install: composer-install

composer-install: ## Install PHP dependencies
	@echo "📦 Installing PHP dependencies..."
	${DOCKER_COMPOSE} exec tms composer install

# Testing commands
test: ## Run PHPUnit tests
	@echo "🧪 Running tests..."
	${DOCKER_COMPOSE} exec tms bin/phpunit
