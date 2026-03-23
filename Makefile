.PHONY: help build build-prod test test-coverage test-php cs-check cs-fix analyse refactor check install clean shell

IMAGE_NAME = purrphp-collection
DEV_IMAGE = $(IMAGE_NAME):dev
PROD_IMAGE = $(IMAGE_NAME):prod

help: ## Show this help message
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

build: ## Build Docker image for development
	docker build --target development -t $(DEV_IMAGE) .

test-coverage: build ## Run tests with coverage report in Docker container
	docker run --rm -v $(PWD)/coverage:/app/coverage $(DEV_IMAGE) composer test-coverage

test-unit: build ## Run only unit tests in Docker container
	docker run --rm $(DEV_IMAGE) ./vendor/bin/phpunit --testsuite="Unit Tests"

cs-check: build ## Check code style in Docker container
	docker run --rm $(DEV_IMAGE) composer cs-check

cs-fix: build ## Fix code style issues in Docker container (copy changes out)
	docker run --name temp-cs-fix $(DEV_IMAGE) composer cs-fix; \
	docker cp temp-cs-fix:/app/src/. ./src/ 2>/dev/null || true; \
	docker cp temp-cs-fix:/app/test/. ./test/ 2>/dev/null || true; \
	docker rm temp-cs-fix

analyse: build ## Run static analysis in Docker container
	docker run --rm $(DEV_IMAGE) composer analyse

check: build ## Run all checks (style, analysis, tests) in Docker container
	docker run --rm $(DEV_IMAGE) composer check

shell: build ## Open interactive shell in development container
	docker run --rm -it $(DEV_IMAGE) sh

install: ## Copy dependencies from built container to local
	docker run --name temp-install $(DEV_IMAGE) ls; \
	docker cp temp-install:/app/composer.lock ./composer.lock 2>/dev/null || true; \
	docker cp temp-install:/app/vendor ./vendor 2>/dev/null || true; \
	docker rm temp-install

update: build ## Update Composer dependencies in Docker container and copy out
	docker run --name temp-update $(DEV_IMAGE) composer update; \
	docker cp temp-update:/app/composer.lock ./composer.lock 2>/dev/null || true; \
	docker cp temp-update:/app/vendor ./vendor 2>/dev/null || true; \
	docker rm temp-update

validate: build ## Validate composer.json in Docker container
	docker run --rm $(DEV_IMAGE) composer validate --strict
