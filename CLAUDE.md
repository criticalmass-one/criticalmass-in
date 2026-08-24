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
# Console commands often need more memory: `php -d memory_limit=-1 bin/console ...`
```

### Migrationen

```bash
php -d memory_limit=-1 bin/console doctrine:migrations:migrate
```

**Die Migrationskette ist nicht von null abspielbar:** `Version20170527205445` ruft
`$platform->getName()`, das es in DBAL 4 nicht mehr gibt. Ein frisches Schema entsteht
deshalb über `doctrine:schema:create` aus dem Mapping, nicht aus den Migrationen — und
für eine neue Migration nimmt man die DDL aus `doctrine:schema:create --dump-sql` gegen
eine Wegwerf-Datenbank, statt `migrations:diff` gegen eine unvollständige Dev-DB laufen
zu lassen. Anschließend mit `doctrine:schema:update --dump-sql` gegenprüfen (muss leer
sein) und `up`/`down` einmal durchspielen.

### Static Analysis
```bash
vendor/bin/phpstan analyse                  # PHPStan level 6
# Baseline: phpstan-baseline.neon — update when adding accepted errors
# If the parallel run crashes in a worker (seen on PHP 8.5), analyse specific
# paths single-process: vendor/bin/phpstan analyse <paths> --debug
```

### Frontend Assets
```bash
# Braucht Node 20+. Liegt eine ältere Version im PATH, bricht encore mit
# "SyntaxError: Unexpected token '?'" ab — dann z. B.
# export PATH="$HOME/.nvm/versions/node/v20.18.1/bin:$PATH"
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

### Anmeldung: passwortlos, drei Wege

Die App kennt **kein Passwort** — `User::getPassword()` gibt fest `''` zurück, es gibt kein
Passwortfeld in der Datenbank. Angemeldet wird über **Magic Link** (`login_link`, Konten
entstehen dabei implizit in `LoginController::createNewUser()`, einen `/register`-Endpunkt
gibt es nicht), über **OAuth** (Facebook/Strava, HWIOAuthBundle) oder über einen
**Passkey**.

**Der Anmeldelink per Mail bleibt für jedes Konto verfügbar.** Passkeys kommen additiv
dazu, sie ersetzen den Rückfallweg nicht — ein Opt-out („Konten mit genug Passkeys dürfen
den Mail-Login abschalten") ist ausdrücklich **nicht** gewollt und soll auch nicht
vorgeschlagen werden. Die bewusst akzeptierte Folge: Die Sicherheitsuntergrenze eines
Kontos ist dauerhaft die Mailbox. Der Gegenwert ist, dass niemand sich aussperren kann.

**Passkeys (WebAuthn)** laufen über `web-auth/webauthn-symfony-bundle`. Was daran nicht
selbsterklärend ist:

- **RP ID ist `criticalmass.one`.** `criticalmass.in`, `www.criticalmass.in` und
  `criticalmass.one` liefern dieselbe App ohne Redirect aus, sind aber teils
  verschiedene registrierbare Domains. Die `.in`-Domains sind über **Related Origin
  Requests** abgedeckt: `webauthn.allowed_origins` wird unter `/.well-known/webauthn`
  ausgeliefert. **Eine Änderung der RP ID entwertet jeden registrierten Passkey.**
- `allowed_origins` **muss literal** in der Config stehen (deshalb Produktionswerte in
  `config/packages/prod/webauthn.yaml`, dev/test in `config/packages/webauthn.yaml`).
  Der Compiler-Pass liest die Liste zur Container-Bauzeit und legt die
  `/.well-known/webauthn`-Route nur an, wenn er ein echtes Array sieht — ein `%env()%`
  wäre dort noch ein String. Aus demselben Grund lässt sich die Liste nicht per
  `when@dev` leeren: Config-Merge ergänzt Listen, er ersetzt sie nicht.
- Der Loader-Eintrag `type: webauthn` in `config/routes.yaml` ist Pflicht, sonst
  existieren weder `/.well-known/webauthn` noch die vier `/passkey/`-Endpunkte.
- **Alle vier Endpunkte sind auf `/passkey/…` umgebogen.** Die Vorgaben des Bundles wären
  `/login/options`, `POST /login`, `/register/options`, `POST /register` — und `POST /login`
  gehört bereits `login_perform`.
- **Sicherheitsrelevant:** Der Firewall-Authenticator verdrahtet für die Registrierung fest
  den `RequestBodyUserEntityGuesser`, der den Benutzernamen aus dem Request-Body nimmt.
  Der Service ist in `config/services.yaml` per Alias auf den sitzungsbasierten
  `CurrentUserEntityGuesser` umgebogen — **diesen Alias nicht entfernen**, sonst kann sich
  jeder einen Passkey auf ein fremdes Konto legen.
- Der **User-Handle** ist `User::$webauthnUserHandle` (UUID, lazy erzeugt), bewusst **nicht**
  die E-Mail: die lässt sich im Profil ohne Re-Verifikation ändern, und mit ihr als Handle
  wären danach alle Passkeys des Kontos tot.
- `App\Entity\WebauthnCredential` erbt von der Mapped Superclass `Webauthn\CredentialRecord`,
  deren XML-Zuordnung aus dem Bundle als eigenes Doctrine-Mapping eingebunden ist. Die
  Credential-ID liegt **base64-kodiert** in der Datenbank (`findOneByCredentialId()` muss
  selbst kodieren) und ist ein `LONGTEXT`, ihr UNIQUE-Index braucht deshalb eine
  Präfixlänge.
- `WebauthnCredentialVoter` hat bewusst **keine `ROLE_ADMIN`-Ausnahme**, anders als
  `TrackVoter`/`PhotoVoter`: Wer fremde Passkeys löschen könnte, sperrt Nutzer aus ihren
  Konten aus.

### Frontend (`assets/`)

Single Webpack Encore entry point (`assets/app.js`). Stimulus controllers in `assets/controllers/` — maps (Leaflet + MapLibre GL), charts (Chart.js), datatables, search, geocoding, ride date checking.

`passkey_controller.js` ist bewusst **ohne** `@simplewebauthn/browser` geschrieben: Die
base64url-Umrechnung können die Browser über `parseCreationOptionsFromJSON()` / `toJSON()`
inzwischen selbst, und eine neue Frontend-Dependency würde die eingefrorene
`package-lock.json` neu auflösen (siehe Frontend note oben). Die Conditional UI hängt an
`autocomplete="username webauthn"` in `LoginType` und ist über
`data-passkey-conditional-value` nur auf der Login-Seite aktiv.

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
