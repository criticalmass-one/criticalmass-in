# MCP-Server

criticalmass.in stellt einen **Model-Context-Protocol-Server** bereit, damit
KI-Assistenten wie **Claude** oder **ChatGPT** mit der Plattform „chatten"
können – Rides einer Stadt abfragen, Teilnahme melden usw.

Der Server spricht **MCP über Streamable HTTP** (JSON-RPC 2.0) und ist per
**OAuth 2.1** (Authorization Code + PKCE) abgesichert. Schreibende Aktionen
laufen immer im Namen des eingeloggten Users.

## Endpunkte

| Zweck | Methode & Pfad |
|---|---|
| MCP-Endpunkt (JSON-RPC) | `POST /mcp` |
| Health-Check | `GET /mcp/health` |
| Protected-Resource-Metadata (RFC 9728) | `GET /.well-known/oauth-protected-resource` |
| Authorization-Server-Metadata (RFC 8414) | `GET /.well-known/oauth-authorization-server` |
| Dynamic Client Registration (RFC 7591) | `POST /oauth2/register` |
| Authorization | `GET /authorize` |
| Token | `POST /token` |

Discovery und Registrierung sind öffentlich; `/mcp` erfordert ein gültiges
Bearer-Token. Der Client durchläuft automatisch: Discovery → (optional) Dynamic
Client Registration → Login + Consent → Token → MCP.

## Scopes

| Scope | Bedeutung |
|---|---|
| `ride:read` | Termine und Rides lesen |
| `city:read` | Städte, Orte und Cycles lesen |
| `track:read` | Tracks lesen |
| `photo:read` | Fotos lesen |
| `post:read` | Forenbeiträge lesen |
| `track:write` | Tracks erstellen und bearbeiten |
| `participation:write` | Teilnahme an Rides melden |
| `estimate:write` | Teilnehmerzahlen schätzen |
| `weather:write` | Wetterdaten zu Rides melden |
| `ride:write` | Rides anlegen und bearbeiten |
| `city:write` | Städte anlegen |
| `cycle:write` | Termin-Zyklen anlegen und bearbeiten |
| `socialnetwork:write` | Social-Network-Profile und -Feeds verwalten |
| `activity:write` | Aktivitäts-Scores von Städten melden |

`tools/list` zeigt nur Werkzeuge, deren Scope das Token besitzt; `tools/call`
weist fehlende Scopes ab. Quelle der Wahrheit ist `App\OAuth2\OAuthScope`.

## Werkzeuge

Die Tools spiegeln die REST-API (`/api/*`).

| Tool | Scope | Gespiegelter Endpunkt |
|---|---|---|
| `list_rides` | `ride:read` | `GET /api/ride` (Filter: Stadt, Region, Typ, Datum, Geo) |
| `get_ride` | `ride:read` | `GET /api/{citySlug}/{rideIdentifier}` |
| `get_current_ride` | `ride:read` | `GET /api/{citySlug}/current` |
| `list_subrides` | `ride:read` | `GET /api/{citySlug}/{rideIdentifier}/subride` |
| `list_cities` | `city:read` | `GET /api/city` |
| `get_city` | `city:read` | `GET /api/{citySlug}` |
| `list_locations` | `city:read` | `GET /api/{citySlug}/location` |
| `list_city_cycles` | `city:read` | `GET /api/{citySlug}/cycles` |
| `list_tracks` | `track:read` | `GET /api/track` |
| `get_track` | `track:read` | `GET /api/track/{id}` |
| `list_ride_tracks` | `track:read` | `GET /api/{citySlug}/{rideIdentifier}/listTracks` |
| `list_photos` | `photo:read` | `GET /api/photo` |
| `get_photo` | `photo:read` | `GET /api/photo/{id}` |
| `list_ride_photos` | `photo:read` | `GET /api/{citySlug}/{rideIdentifier}/listPhotos` |
| `list_posts` | `post:read` | `GET /api/post` |
| `set_participation` | `participation:write` | Teilnahme melden (`yes`/`maybe`/`no`) |
| `create_ride_estimate` | `estimate:write` | `POST /api/estimate` |
| `set_weather` | `weather:write` | `PUT /api/{citySlug}/{rideIdentifier}/weather` |
| `create_ride`, `update_ride` | `ride:write` | `PUT`/`POST /api/{citySlug}/{rideIdentifier}` |
| `create_city` | `city:write` | `PUT /api/{citySlug}` |
| `create_cycle`, `update_cycle` | `cycle:write` | `PUT`/`POST /api/{citySlug}/cycles` |
| `create_social_profile`, `update_social_profile` | `socialnetwork:write` | `/api/{citySlug}/socialnetwork-profiles` |
| `create_social_feeditem`, `update_social_feeditem` | `socialnetwork:write` | `/api/{citySlug}/socialnetwork-feeditems` |
| `create_city_activity` | `activity:write` | `POST /api/city/{citySlug}/activity` |

Schreibende Tools erben von `AbstractWriteTool` (Deserialisierung mit der Group
`api-write`, Validierung, Persistenz wie im API-BaseController), lesende
Listen-Tools von `AbstractDataQueryTool`. Alle implementieren
`App\Mcp\Tool\McpToolInterface` und werden über den Tag `app.mcp_tool`
automatisch registriert.

Mächtige Schreib-Tools (Anlage/Änderung von Rides, Städten, Cycles, Social-
Network-Daten, Aktivitäts-Scores) sind über eigene `*:write`-Scopes abgesichert
und damit nur mit ausdrücklicher Nutzer-Zustimmung im OAuth-Consent nutzbar.

## Client einrichten

- **Claude / ChatGPT (Remote Connector):** als Server-URL die MCP-URL angeben
  (`https://<host>/mcp`). Der Connector findet Authorization-Server und
  Registrierung selbstständig über die Well-Known-Endpunkte.
- **Claude Desktop / Code:** Remote-MCP-Server mit derselben URL eintragen.

## Setup (Server)

Der OAuth-Authorization-Server (`league/oauth2-server-bundle`) braucht JWT-Keys
und Secrets (siehe `.env`-Block `league/oauth2-server-bundle`):

```bash
# Schlüsselpaar erzeugen (Passphrase = OAUTH_PASSPHRASE aus .env)
openssl genpkey -algorithm RSA -out config/jwt/private.pem -aes256 \
  -pass "pass:$OAUTH_PASSPHRASE" -pkeyopt rsa_keygen_bits:2048
openssl pkey -in config/jwt/private.pem -passin "pass:$OAUTH_PASSPHRASE" \
  -pubout -out config/jwt/public.pem

# OAuth2-Tabellen anlegen
php bin/console doctrine:migrations:migrate
```

Die Test-Keys unter `config/jwt/test/` sind bewusst eingecheckt (nur
`APP_ENV=test`, keine Geheimnisse); die Dev-/Prod-Keys bleiben gitignored.
