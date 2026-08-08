# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**criticalmass.in** — web platform for coordinating and documenting Critical Mass bicycle rides worldwide. Manages cities, rides/events, participants, GPS tracks, photos, forums, and statistics.

**Stack:** Symfony 7.4 (LTS), Doctrine ORM 3 / DBAL 4, PHP 8.2+, MariaDB 10.9+, Bootstrap 5, Webpack Encore with Stimulus

## Common Commands

### Testing
```bash
composer test              # Full cycle: drop DB, create schema, load fixtures, run PHPUnit
composer test:run          # Just run PHPUnit (no DB reset)
composer test:api          # Only API test suite
vendor/bin/phpunit tests/Path/To/TestFile.php              # Single test file
vendor/bin/phpunit --filter testMethodName                  # Single test method
# Controller/DB tests need MariaDB up (docker-compose up); otherwise they fail with
# "getaddrinfo for mysql failed". Pure unit tests (no DB) run standalone.
# Use `php bin/console ...` (the bare `bin/console` may report "permission denied").
```

**Time-dependent tests:** `phpunit.dist.xml` wires Symfony's `SymfonyExtension` with
`clock-mock-namespaces=App`, so unqualified `time()` calls inside `App\…` are mockable.
That is why e.g. `RideRepository` builds "now" via
`\DateTime::createFromFormat('U', (string)time())` — do not "simplify" this to
`new \DateTime()`. Test pattern (see `tests/Repository/RideRepositoryTest.php`):
tag the test `#[Group('time-sensitive')]`, then `ClockMock::register(TheClass::class)` +
`ClockMock::withClockMock($timestamp)`; reset with `ClockMock::withClockMock(false)` in
`tearDown()`. Fixtures compute ride dates **relative to load time**
(`RideFixtures`: `"±N months last friday 19:00"`), so tests must not assume absolute
dates — read the fixture entity's datetime from the DB and set the mock clock relative
to it.

**"Current ride" semantics:** `RideRepository::findCurrentRideForCity()` returns the next
ride with `dateTime >= now − CURRENT_RIDE_GRACE_HOURS` (4 h), i.e. a ride that already
started stays "current" during the grace period — on the city page, city list,
`/api/{citySlug}/current` and the MCP `GetCurrentRideTool`.

### Static Analysis
```bash
vendor/bin/phpstan analyse                  # PHPStan level 6
# Baseline: phpstan-baseline.neon — update when adding accepted errors
# If the parallel run crashes in a worker (seen on PHP 8.5), analyse specific
# paths single-process: vendor/bin/phpstan analyse <paths> --debug
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

### Unified Upload (tracks + photos)

Users upload GPX/FIT **tracks and photos through one form** at `/upload` (`UnifiedUploadController`, Uppy dashboard, one POST per file to `/upload/file`). The Strava data-import path is being retired: the API Agreement's retention/deletion rules (Policy §6.2/§6.3/§7.4) forbid permanently and publicly archiving API-sourced data (epic #1388). `UploadDispatcher` (`Criticalmass/Upload/`) routes each file by extension to a handler that parses it into a **candidate** and either matches it to a ride or parks it for review:

- **Tracks** (`.gpx`, `.fit`) → `TrackUploadHandler` → `UploadedTrackCandidateFactory` → `TrackImportCandidate` → `TrackDecider` (voters in `Criticalmass/MassTrackImport/Voter/`, threshold 0.75, wired via `TrackVoterPass`) → `FileTrackImporter` turns a confirmed candidate into a `Track`. FIT is normalised to GPX on ingest (`Geo/FitService/FitToGpxConverter`), so everything downstream stays GPX-only.
- **Images** (`.jpg/.jpeg/.png/.webp/.gif/.heic/.heif`) → `PhotoUploadHandler` → `PhotoCandidateFactory` → `PhotoImportCandidate` (staged **outside the web root** in `var/photo-candidates`, #1395) → `PhotoDecider` (date + GPS proximity) → `PhotoCandidateImporter` turns a confirmed gallery into `Photo`s via the normal `PhotoUploadedEvent` enrichment. HEIC/HEIF are normalised to JPEG on ingest (`PhotoImport/Normalizer/ImagickPhotoNormalizer`, EXIF preserved for date/GPS matching).

The photo pipeline in `Criticalmass/PhotoImport/` deliberately mirrors the track pipeline in `Criticalmass/MassTrackImport/` (`PhotoImportCandidate`↔`TrackImportCandidate`, factory/decider/importer, HEIC→JPEG ↔ FIT→GPX). Both candidate entities are source-agnostic (`source`/`fileHash`/`originalName`).

Everything is confirmed on **one review page** at `/upload/review` (`UnifiedReviewController`): **photos are reviewed per whole gallery** (grouped by capture date via `UploadReviewAssembler`, never per single photo) — confirm to the suggested ride, reassign to another ride **on the same date**, or reject the whole gallery; tracks are confirmed/rejected or reassigned to a same-date ride. The old `/trackupload/bulk` (Dropzone) and `/trackupload/review` routes redirect here. Housekeeping: `criticalmass:photos:purge-import-candidates` (mirrors the track variant, #1387). Per-ride single uploads still exist: `TrackUploadController` (`/{city}/{ride}/addtrack`) and `PhotoUploadController` (`/{city}/{ride}/addphoto`).

**Frontend note:** the uploader is **Uppy** (`assets/controllers/unified_upload_controller.js`), no Compressor plugin so image EXIF survives. The `@uppy/*` packages are declared in `package.json`, but `package-lock.json` is **frozen** — `webpack`/`@babel/core` are not declared deps and survive only via the committed lock, so any re-resolution (`npm install`/`yarn install`) breaks the tree; use `npm ci`, and regenerate the lock deliberately when adding frontend deps. Frontend assets are **not** built in CI.

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
