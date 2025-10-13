# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

Project overview
- Minimal PHP framework inspired by Laravel concepts (from a course sample). It includes:
  - Attribute-based routing via custom Router and Route/Get attributes
  - A lightweight DI container with autowiring (PSR-11 compatible interface)
  - Thin MVC-style layers: Controllers, Models with a PDO wrapper, and a simple View renderer
  - Optional Docker setup (PHP-FPM + Nginx + MySQL)

Common development commands
Dependencies and autoload
- Install dependencies: composer install
- Regenerate autoload: composer dump-autoload -o

Environment
- Copy example env to active env (PowerShell): Copy-Item .env.example .env
- The app reads DB settings from .env via vlucas/phpdotenv. Required keys: DB_HOST, DB_USER, DB_PASS, DB_DATABASE, DB_DRIVER (defaults to mysql if unset).

Run locally (without Docker)
- Start the PHP dev server (serves public/index.php): php -S 127.0.0.1:8000 -t public
- Then open http://127.0.0.1:8000

Run via Docker (recommended for full stack including MySQL)
- Build and start: docker compose -f docker/docker-compose.yml up -d --build
- Stop: docker compose -f docker/docker-compose.yml down
- Services:
  - Nginx on http://localhost:8000 serving public/
  - PHP-FPM (app) container
  - MySQL 8.0 exposed on localhost:3307 with MYSQL_ROOT_PASSWORD=root, MYSQL_DATABASE=my_db

Testing
- Run all tests: vendor/bin/phpunit
- Run a specific test file: vendor/bin/phpunit tests/Unit/Services/InvoiceServiceTest.php
- Run a single test by name: vendor/bin/phpunit --filter it_processes_invoice tests/Unit/Services/InvoiceServiceTest.php

Linting / syntax checks
- No formatter/config present in the repo. For a quick syntax check in PowerShell:
  - Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }

High-level architecture
- Entry point: public/index.php
  - Loads Composer autoload and environment variables
  - Defines constants for storage and views
  - Instantiates Container and Router, registers controllers by attributes, and boots App
- App kernel: app/App.php
  - Holds Container, Router, request, Config, and static DB
  - Binds PaymentGatewayServiceInterface to PaymentGatewayService in the container
  - Handles request dispatch and 404 fallback to views/error/404.php
- Dependency Injection Container: app/Container.php
  - Simple PSR-11 ContainerInterface implementation
  - Autowires dependencies via Reflection (constructor inspection)
  - Supports manual bindings via set(id, concrete)
- Routing: app/Router.php and attributes in app/Attributes/
  - Route and Get attributes annotate controller methods with route path and method
  - registerRouteFromAttribute() reflects controller classes to auto-register routes
  - resolve() dispatches to closures or [Class, method] handlers through the container
- Controllers: app/Controllers/
  - Example: HomeController uses InvoiceService and returns a View
  - Example: GeneratorExampleController pulls records via a Model and streams output
- Models and DB: app/Model.php, app/DB.php, app/Models/
  - DB is a thin wrapper over PDO with reasonable defaults
  - Base Model fetchLazy() provides generator-based iteration
  - Example model: Ticket::all() yields records lazily
- Views: app/View.php and views/
  - View::make('name', params) loads views/<name>.php and exposes params as variables
  - __toString() renders to string, enabling echo of View instances
- Configuration: app/Config.php
  - Wraps env-driven configuration (db array), read-only via magic __get
- Tests: tests/Unit/
  - Router tests cover registration and resolution, exceptions on missing routes
  - Service tests demonstrate mocking and behavior assertions for InvoiceService

Docker specifics
- docker/nginx/nginx.conf
  - Serves from /var/www/public
  - Proxies PHP requests to app:9000 (PHP-FPM)
- docker/docker-compose.yml
  - Mounts project root into /var/www inside containers for live editing

Notes
- Composer configuration (composer.json): PSR-4 autoload maps App\\ to app/, and tests under Tests\\ to tests/
- PHPUnit bootstrap (phpunit.xml) loads vendor/autoload.php and targets tests/Unit
