# Critical Mass

Eine Webplattform zur Koordination und Dokumentation von Critical-Mass-Fahrradtouren weltweit.

## Funktionen

### Städte und Touren
- **Städteverzeichnis**: Übersicht aller Städte mit Critical-Mass-Touren
- **Tourenkalender**: Termine und Details zu allen geplanten Touren
- **Wiederkehrende Termine (Cycles)**: Automatische Generierung von Touren basierend auf Regeln (z.B. "jeden letzten Freitag im Monat")
- **Subrides**: Unterstützung für Nebentouren und Zubringer
- **Regionen**: Geografische Gruppierung von Städten

### Teilnahme und Community
- **Teilnahmebestätigung**: Nutzer können ihre Teilnahme an Touren ankündigen
- **Teilnehmerschätzungen**: Crowdsourced Schätzungen der Teilnehmerzahlen
- **Diskussionsforen**: Boards und Threads für den Austausch
- **Benutzerprofile**: Persönliche Profile mit Teilnahmehistorie

### Dokumentation
- **Fotogalerie**: Upload und Verwaltung von Tourenfotos
- **GPS-Tracks**: Upload und Visualisierung von GPX-Tracks
- **Strava-Integration**: Import von Tracks direkt aus Strava
- **Wetterdaten**: Automatische Wetterdokumentation für Touren
- **Timeline**: Aktivitätsfeed aller Ereignisse

### Karten und Visualisierung
- **Interaktive Karten**: Leaflet/MapLibre-basierte Kartenansichten
- **Streckendarstellung**: Visualisierung von GPS-Tracks auf Karten
- **Cluster-Ansicht**: Übersichtliche Darstellung vieler Marker

### Social Media
- **Social-Network-Profile**: Verknüpfung mit Facebook, Twitter, Instagram etc.
- **Feed-Integration**: Aggregation von Social-Media-Posts

### API
- **REST API**: Vollständige REST-Schnittstelle unter `/api/`
- **API-Dokumentation**: Interaktive Dokumentation unter `/api/doc`

### Statistiken
- **Monatsstatistiken**: Auswertungen nach Zeiträumen
- **Top-Listen**: Rankings nach verschiedenen Kriterien

## Systemvoraussetzungen

### Erforderlich
- **PHP** >= 8.5
- **MySQL/MariaDB** >= 10.9
- **Node.js** >= 18
- **Yarn** >= 3.4 oder npm
- **Composer** >= 2.0

### PHP-Extensions
- `intl`
- `simplexml`
- `fileinfo`
- `xmlwriter`
- `json`
- `zip`
- `pdo_mysql`

### Optional (für vollständige Funktionalität)
- **Redis**: Caching und Sessions
- **RabbitMQ**: Asynchrone Verarbeitung (View-Counting, Track-Import)
- **Memcached**: Alternatives Caching

## Lokale Entwicklung

### 1. Repository klonen

```bash
git clone https://github.com/criticalmass-one/criticalmass-in.git
cd criticalmass-in
```

### 2. Docker-Services starten

Die Datenbank und weitere Services werden via Docker bereitgestellt:

```bash
docker-compose up -d
```

Dies startet:
- **MariaDB** auf Port 8002 (extern) / 3306 (intern)
- **Redis** für Caching
- **Memcached** für Caching
- **Mailcatcher** für E-Mail-Tests (Web-UI auf Port 1080)

### 3. Umgebungsvariablen konfigurieren

```bash
cp .env .env.local
```

Die `.env.local` nach Bedarf anpassen. Die Standardwerte funktionieren mit den Docker-Services.

### 4. PHP-Abhängigkeiten installieren

```bash
composer install
```

### 5. Frontend-Assets installieren und bauen

```bash
yarn install
yarn build
```

Für die Entwicklung mit Hot-Reload:

```bash
yarn watch
# oder
yarn dev-server
```

### 6. Datenbank einrichten

```bash
# Datenbank-Schema erstellen
php bin/console doctrine:schema:create

# Optional: Fixtures laden (Testdaten)
php bin/console doctrine:fixtures:load
```

### 7. Symfony-Server starten

Mit Symfony CLI (empfohlen):

```bash
symfony serve
```

Oder mit PHP Built-in Server:

```bash
php -S localhost:8000 -t public/
```

Die Anwendung ist dann unter http://localhost:8000 erreichbar.

## Konfiguration

### Datenbank

```env
DATABASE_URL=mysql://criticalmass:criticalmass@127.0.0.1:8002/criticalmass
```

### OAuth (Facebook, Strava)

Für Social Login müssen entsprechende App-Credentials konfiguriert werden:

```env
FACEBOOK_APP_ID=your_app_id
FACEBOOK_APP_SECRET=your_app_secret

STRAVA_CLIENT_ID=your_client_id
STRAVA_SECRET=your_secret
```

### RabbitMQ

Für asynchrone Verarbeitung:

```env
RABBITMQ_URL=amqp://guest:guest@localhost:5672
```

### Redis

```env
REDIS_URL=redis://localhost:6379
```

## Tests

### Test-Datenbank einrichten und Tests ausführen

```bash
composer test
```

Dies führt folgende Schritte aus:
1. Test-Datenbank zurücksetzen
2. Schema erstellen
3. Fixtures laden
4. PHPUnit-Tests ausführen

### Nur Tests ausführen (ohne DB-Reset)

```bash
composer test:run
# oder
./vendor/bin/phpunit
```

### Nur API-Tests

```bash
composer test:api
```

## Projektstruktur

```
criticalmass-in/
├── assets/              # Frontend-Assets (JS, CSS, Images)
├── bin/                 # Konsolenbefehle
├── config/              # Symfony-Konfiguration
│   ├── packages/        # Bundle-Konfigurationen
│   └── routes/          # Routing-Konfiguration
├── docker/              # Docker-Volumes
├── public/              # Web-Root
│   ├── photos/          # Hochgeladene Fotos
│   └── tracks/          # Hochgeladene GPS-Tracks
├── src/
│   ├── Command/         # Konsolenbefehle
│   ├── Controller/      # HTTP-Controller
│   │   └── Api/         # REST-API-Controller
│   ├── Criticalmass/    # Domain-Logik
│   ├── Entity/          # Doctrine-Entities
│   ├── Form/            # Symfony-Formulare
│   ├── Menu/            # Menü-Builder
│   ├── Repository/      # Doctrine-Repositories
│   ├── Serializer/      # Serializer-Komponenten
│   └── Twig/            # Twig-Extensions
├── templates/           # Twig-Templates
├── tests/               # PHPUnit-Tests
├── translations/        # Übersetzungsdateien
└── var/                 # Cache und Logs
```

## Wichtige Konsolen-Befehle

```bash
# Cache leeren
php bin/console cache:clear

# Datenbank-Migrationen
php bin/console doctrine:migrations:migrate

# Routen anzeigen
php bin/console debug:router

# Services anzeigen
php bin/console debug:container

# Assets installieren
php bin/console assets:install public
```

## API-Dokumentation

Die REST-API ist unter `/api/` verfügbar. Eine interaktive Dokumentation (Swagger/OpenAPI) findest du unter:

```
http://localhost:8000/api/doc
```

### API-Endpunkte (Auswahl)

| Methode | Endpunkt | Beschreibung |
|---------|----------|--------------|
| GET | `/api/city` | Liste aller Städte |
| GET | `/api/{citySlug}` | Details einer Stadt |
| GET | `/api/{citySlug}/{rideIdentifier}` | Details einer Tour |
| GET | `/api/ride` | Liste von Touren |
| GET | `/api/photo` | Liste von Fotos |
| GET | `/api/track` | Liste von Tracks |
| POST | `/api/estimate` | Teilnehmerschätzung abgeben |

## Technologie-Stack

### Backend
- **Symfony 6** - PHP-Framework
- **Doctrine ORM** - Datenbank-Abstraktion
- **Twig** - Template-Engine

### Frontend
- **Bootstrap 5** - CSS-Framework
- **Webpack Encore** - Asset-Bundling
- **Stimulus** - JavaScript-Framework
- **Leaflet/MapLibre** - Kartenvisualisierung
- **Chart.js** - Diagramme

### Infrastruktur
- **MariaDB/MySQL** - Datenbank
- **Redis** - Caching
- **RabbitMQ** - Message Queue
- **Docker** - Containerisierung

## Lizenz

MIT License

## Autor

Malte Hübner - [malte@caldera.cc](mailto:malte@caldera.cc)
