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
| `track:read` | Tracks lesen |
| `track:write` | Tracks erstellen und bearbeiten |
| `participation:write` | Teilnahme an Rides melden |
| `estimate:write` | Teilnehmerzahlen schätzen |

`tools/list` zeigt nur Werkzeuge, deren Scope das Token besitzt; `tools/call`
weist fehlende Scopes ab. Quelle der Wahrheit ist `App\OAuth2\OAuthScope`.

## Werkzeuge

| Tool | Scope | Beschreibung |
|---|---|---|
| `list_city_rides` | `ride:read` | Rides einer Stadt (per Slug) auflisten |
| `set_participation` | `participation:write` | Eigene Teilnahme (`yes`/`maybe`/`no`) melden |

Neue Werkzeuge implementieren `App\Mcp\Tool\McpToolInterface` und werden über
den Tag `app.mcp_tool` automatisch registriert.

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
