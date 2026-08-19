# GEMINI.md - Symfony 7.4 Project (Association CMS, public release)

This project is a starter kit for Symfony 7.4 / PHP 8.4 applications, using Docker for the development environment.

## Project Overview

- **Framework:** Symfony 7.4
- **Language:** PHP 8.4
- **Database:** PostgreSQL 18
- **Frontend:** Twig with Asset Mapper (Importmap)
- **Admin Interface:** EasyAdmin 5.0
- **Development Environment:** Docker & Docker Compose

## Architecture & Conventions

### Domain Layer (Entities & Repositories)
- **Entities:** Located in `src/Entity/`. Most entities should extend `App\Entity\AbstractEntity` which provides a numeric `id`, a `uuid`, and lifecycle callbacks for timestamps.
- **Traits:** `App\Trait\Timestampable` is used for `createdAt` and `updatedAt` fields.
- **Repositories:** Located in `src/Repository/`. They should extend `App\Repository\AbstractRepository` which includes utility methods for pagination and basic CRUD.

### Web Layer (Controllers & Templates)
- **Controllers:** Located in `src/Controller/`. Controllers typically extend `App\Controller\AbstractBaseController` for shared pagination DTO creation and flash message helpers.
- **Templates:** Located in `templates/`. Uses Twig with a base layout in `templates/base.html.twig`.
- **Translations:** ICU message files in `translations/` (e.g., `messages+intl-icu.fr.yaml`). The default locale is French.
- **Pagination:** Pagerfanta is used for pagination. `AbstractRepository::findAllPaginated` returns a `Pagerfanta` instance.

### Development Commands (via Makefile)
Use `make` commands from the host machine to interact with the Docker containers.

| Task | Command |
| --- | --- |
| Full Installation | `make install` |
| Start Containers | `make start` |
| Stop Containers | `make stop` |
| PHP Shell Access | `make connect` |
| Clear Symfony Cache | `make clear` |
| Database Migrations | `make migrations` |
| Asset Installation | `make assets-install` |

### Key Custom Commands
Run these inside the PHP container (`make connect`):

- **Create Admin:** `php bin/console app:user:create-admin --email=admin@example.com --password=secret`
- **Import Content:** `php bin/console app:import:content <type> <path_to_file>` (types: `categories`, `articles`, `resources`)

## Quality & Testing

### Code Quality
- **Static Analysis:** `vendor/bin/phpstan analyse` (Targeting level 6)
- **Coding Standard:** `vendor/bin/phpcs` (PSR-12)
- **Auto-Fix Style:** `vendor/bin/phpcbf`

### Testing
- **PHPUnit:** `php bin/phpunit`
- Configuration is in `phpunit.xml.dist`.

## Environment Variables
Environment configuration is managed via `.env` and `.env.test`.
- `APP_PORT`: Default `8081`
- `DATABASE_URL`: Connection string for PostgreSQL
- `MAILER_DSN`: Configured for Mailpit (`smtp://mailer:1025`)
