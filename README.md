# Critical Mass

## API

Das Projekt bietet zwei API-Schnittstellen:

- **REST API** unter `/api/`
- **GraphQL API** unter `/graphql/`

### GraphQL API

#### Endpoint

```
POST /graphql/
Content-Type: application/json
```

#### GraphiQL Playground (nur Dev-Umgebung)

In der Entwicklungsumgebung steht unter `/graphiql/` ein interaktiver GraphQL-Playground zur Verfügung.

#### Beispiele mit curl

**Alle Städte abrufen:**

```bash
curl -X POST http://localhost:8000/graphql/ \
  -H "Content-Type: application/json" \
  -d '{"query": "{ cities(limit: 5) { id name slug latitude longitude } }"}'
```

**Eine Stadt mit Slug abrufen:**

```bash
curl -X POST http://localhost:8000/graphql/ \
  -H "Content-Type: application/json" \
  -d '{"query": "{ city(slug: \"hamburg\") { name title description latitude longitude timezone } }"}'
```

**Stadt mit ihren Rides abrufen:**

```bash
curl -X POST http://localhost:8000/graphql/ \
  -H "Content-Type: application/json" \
  -d '{"query": "{ city(slug: \"hamburg\") { name rides(limit: 3) { title dateTime location estimatedParticipants } } }"}'
```

**Rides einer Stadt abrufen:**

```bash
curl -X POST http://localhost:8000/graphql/ \
  -H "Content-Type: application/json" \
  -d '{"query": "{ rides(limit: 10, citySlug: \"hamburg\") { id title dateTime location } }"}'
```

**Einzelnen Ride mit Photos und Tracks:**

```bash
curl -X POST http://localhost:8000/graphql/ \
  -H "Content-Type: application/json" \
  -d '{"query": "{ ride(id: 123) { title dateTime city { name } photos(limit: 5) { id imageName } tracks { username distance } weather { temperatureDay weatherDescription } } }"}'
```

**Photos abrufen:**

```bash
curl -X POST http://localhost:8000/graphql/ \
  -H "Content-Type: application/json" \
  -d '{"query": "{ photos(limit: 10, citySlug: \"hamburg\") { id imageName description views } }"}'
```

**Tracks abrufen:**

```bash
curl -X POST http://localhost:8000/graphql/ \
  -H "Content-Type: application/json" \
  -d '{"query": "{ tracks(limit: 5) { id username distance points polyline } }"}'
```

**Locations einer Stadt:**

```bash
curl -X POST http://localhost:8000/graphql/ \
  -H "Content-Type: application/json" \
  -d '{"query": "{ locations(citySlug: \"hamburg\") { id title slug latitude longitude } }"}'
```

#### Beispiele mit HTTPie

```bash
# Städte abrufen
http POST localhost:8000/graphql/ query='{ cities(limit: 5) { name slug } }'

# Stadt mit Rides
http POST localhost:8000/graphql/ query='{ city(slug: "hamburg") { name rides(limit: 3) { title dateTime } } }'
```

#### Verfügbare Queries

| Query | Parameter | Beschreibung |
|-------|-----------|--------------|
| `cities` | `limit: Int` | Liste aller Städte |
| `city` | `slug: String!` | Stadt nach Slug |
| `cityById` | `id: ID!` | Stadt nach ID |
| `rides` | `limit: Int`, `citySlug: String` | Liste von Rides |
| `ride` | `id: ID!` | Ride nach ID |
| `rideBySlug` | `citySlug: String!`, `date: String!` | Ride nach Stadt und Datum |
| `photos` | `limit: Int`, `citySlug: String`, `rideId: ID` | Liste von Photos |
| `photo` | `id: ID!` | Photo nach ID |
| `tracks` | `limit: Int`, `rideId: ID` | Liste von Tracks |
| `track` | `id: ID!` | Track nach ID |
| `locations` | `citySlug: String!` | Locations einer Stadt |
| `location` | `id: ID!` | Location nach ID |

#### Typen

**City**
- `id`, `name`, `title`, `slug`, `description`, `latitude`, `longitude`, `timezone`, `cityPopulation`
- `rides(limit)`, `locations`, `socialNetworkProfiles`

**Ride**
- `id`, `title`, `slug`, `description`, `dateTime`, `latitude`, `longitude`, `location`
- `estimatedParticipants`, `estimatedDistance`, `estimatedDuration`
- `city`, `photos(limit)`, `tracks`, `weather`

**Photo**
- `id`, `latitude`, `longitude`, `description`, `views`, `imageName`, `location`, `exifCreationDate`
- `ride`, `city`

**Track**
- `id`, `username`, `distance`, `points`, `startDateTime`, `endDateTime`, `polyline`, `enabled`
- `ride`

**Location**
- `id`, `title`, `slug`, `description`, `latitude`, `longitude`
- `city`

**Weather**
- `id`, `temperatureMin`, `temperatureMax`, `temperatureMorning`, `temperatureDay`, `temperatureEvening`, `temperatureNight`
- `precipitation`, `weatherDescription`, `weatherIcon`

### REST API

Die REST API ist unter `/api/` verfügbar. Dokumentation unter `/api/doc`.
