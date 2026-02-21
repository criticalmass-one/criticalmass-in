# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**criticalmass.in** — web platform for coordinating and documenting Critical Mass bicycle rides worldwide. Manages cities, rides/events, participants, GPS tracks, photos, forums, and statistics.

**Stack:** Symfony 7.2, Doctrine ORM 3 / DBAL 4, PHP 8.2+, MariaDB 10.9+, Bootstrap 5, Webpack Encore with Stimulus

## Common Commands

### Testing
```bash
composer test              # Full cycle: drop DB, create schema, load fixtures, run PHPUnit
composer test:run          # Just run PHPUnit (no DB reset)
composer test:api          # Only API test suite
vendor/bin/phpunit tests/Path/To/TestFile.php              # Single test file
vendor/bin/phpunit --filter testMethodName                  # Single test method
```

### Static Analysis
```bash
vendor/bin/phpstan analyse                  # PHPStan level 6
# Baseline: phpstan-baseline.neon — update when adding accepted errors
```

### Frontend Assets
```bash
yarn dev          # Build once for development
yarn watch        # Dev build with file watching
yarn build        # Production build
```

### Docker Services
```bash
docker-compose up -d      # MariaDB (port 8002), Redis, Memcached, Mailcatcher (port 1080)
```

## Architecture

### Source Layout (`src/`)

- **`Entity/`** — 25 Doctrine entities. Core: `City`, `Ride`, `Track`, `Photo`, `User`, `Participation`, `RideEstimate`. Entities use PHP attribute mapping and implement interfaces from `EntityInterface/` (e.g. `CoordinateInterface`, `PhotoInterface`, `RouteableInterface`).
- **`Controller/`** — Web controllers + `Controller/Api/` for REST API endpoints (documented via NelmioApiDocBundle at `/api/doc`)
- **`Criticalmass/`** — Domain logic (~30 sub-namespaces): `Image/` (photo processing), `Geo/` (GPS/coordinates), `DataQuery/` (API filtering), `Participation/`, `Statistic/`, `Strava/` (import), `Timeline/`, `RideNamer/`, `Router/` (custom entity routing), etc.
- **`Repository/`** — One Doctrine repository per entity
- **`Command/`** — Console commands: `Cycles/`, `Photo/`, `Track/`, `Statistic/`, `SocialNetwork/`
- **`EventSubscriber/`** — Domain event subscribers for Photo, Track, Participation, RideEstimate, etc.
- **`ValueResolver/`** — Symfony argument resolvers for `City`, `Region`, `Ride`, `Thread` (resolved from route slugs)
- **`Twig/Extension/`** — 9 custom extensions (Router, DateTime, Seo, SocialNetwork, etc.)

### Custom Entity Router

Notable pattern: entities are annotated with `#[Routing\DefaultRoute]` and `#[Routing\RouteParameter]` attributes. The `DelegatedRouterManager` in `src/Criticalmass/Router/` generates canonical URLs for any entity by introspecting these attributes. Used extensively in Twig via `RouterTwigExtension`.

### Frontend (`assets/`)

Single Webpack Encore entry point (`assets/app.js`). Stimulus controllers in `assets/controllers/` — maps (Leaflet + MapLibre GL), charts (Chart.js), datatables, search, geocoding, ride date checking.

### Tests (`tests/`)

Mirror `src/` structure. Controller tests extend `AbstractControllerTestCase`. Domain tests cover entities, serializers, validators, ride namer, geo, participation, statistics, etc. PHPUnit 11, `APP_ENV=test`.

## Git-Workflow

- **Niemals direkt auf `main` committen** — immer einen passenden Feature-/Bugfix-Branch erstellen (z.B. `feature/add-xyz`, `fix/broken-abc`, `security/fix-xyz`)
- **Kein Squash Commit und kein Squash Merge** — alle Commits bleiben einzeln erhalten

## Pull Requests

### Labels
Bei jedem neuen PR automatisch passende Labels setzen:

- **`AI-generated`** — Immer setzen bei von Claude erstellten PRs
- **`PHP`** — Bei Änderungen an PHP-Dateien
- **`Twig`** — Bei Änderungen an Twig-Templates
- **`javascript`** — Bei Änderungen an JavaScript-Dateien
- **`bug`** — Bei Bugfixes
- **`enhancement`** — Bei neuen Features oder Verbesserungen
- **`dependencies`** — Bei Änderungen an composer.json, package.json, yarn.lock

### PR-Erstellung
- Titel kurz und prägnant (unter 70 Zeichen)
- Body mit Summary (Bullet Points) und Test Plan
- Am Ende: `🤖 Generated with [Claude Code](https://claude.com/claude-code)`

## Code Style

- PHP: Symfony conventions, `declare(strict_types=1)`
- Templates: Twig with Bootstrap 5 form theme
- Entity mapping: PHP attributes (not annotations)
- Validation: PHP attributes (`#[Attribute]`, not `@Annotation`)

## CI/CD

- PHPStan and PHPUnit must pass
- Bei PHPStan-Fehlern: `phpstan-baseline.neon` aktualisieren
