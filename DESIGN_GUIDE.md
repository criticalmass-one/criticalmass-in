# Design Guide - criticalmass.in

Dieser Guide beschreibt die Design-Prinzipien und Muster für die Gestaltung von Seiten auf criticalmass.in. Er basiert auf Bootstrap 5 und den Redesign-Arbeiten an der Ride-Seite.

## Grundprinzipien

### Cards ohne Borders
```twig
<div class="card border-0 shadow-sm">
    ...
</div>
```
- Verwende `border-0` für randlose Cards
- `shadow-sm` für dezenten Schatten
- `bg-light` für Hintergrund-Cards ohne Schatten

### Konsistente Abstände
- `mb-3` / `mb-4` zwischen Sektionen
- `g-3` für Row-Gaps
- `me-2` zwischen Icon und Text

---

## Hero Section Pattern

Für Hauptseiten mit Karte oder großem visuellen Element:

```twig
<div class="card mb-4 border-0 shadow-sm overflow-hidden">
    <div class="row g-0">
        {# Visueller Bereich (Karte/Bild) - 7 Spalten #}
        <div class="col-lg-7">
            <div class="position-relative h-100" style="min-height: 300px;">
                {# Inhalt #}

                {# Optional: Overlay-Button oben rechts #}
                <button class="btn btn-light btn-sm position-absolute top-0 end-0 m-2 shadow" style="z-index: 1000;">
                    <i class="far fa-expand"></i>
                </button>
            </div>
        </div>

        {# Info-Bereich - 5 Spalten #}
        <div class="col-lg-5">
            <div class="card-body d-flex flex-column h-100">
                {# Titel #}
                <div class="mb-3">
                    <h1 class="h3 mb-1">Titel</h1>
                    <a href="#" class="text-muted text-decoration-none">
                        <i class="far fa-map-marker-alt me-1"></i>Untertitel
                    </a>
                </div>

                {# Details im zweispaltigen Grid #}
                <div class="row g-3 mb-3">
                    {# Detail-Items hier #}
                </div>

                {# Aktions-Buttons am unteren Rand #}
                <div class="mt-auto">
                    <div class="btn-group w-100" role="group">
                        {# Buttons #}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## Icon-Badge Pattern

Für Informationen mit Icon im zweispaltigen Layout:

```twig
<div class="col-6">
    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2"
             style="width: 36px; height: 36px; min-width: 36px;">
            <i class="far fa-calendar-alt text-primary"></i>
        </div>
        <div>
            <small class="text-muted d-block">Label</small>
            <strong>Wert</strong>
        </div>
    </div>
</div>
```

### Farbvarianten für Icons
| Typ | Hintergrund | Icon-Farbe |
|-----|-------------|------------|
| Primär (Datum) | `bg-primary bg-opacity-10` | `text-primary` |
| Erfolg (Zeit) | `bg-success bg-opacity-10` | `text-success` |
| Warnung (Ort) | `bg-warning bg-opacity-10` | `text-warning` |
| Info (Wetter) | `bg-info bg-opacity-10` | `text-info` |
| Sekundär (Stats) | `bg-secondary bg-opacity-10` | `text-secondary` |

---

## Button-Gruppe Pattern

Für Aktions-Buttons:

```twig
<div class="btn-group w-100" role="group">
    <a href="#" class="btn btn-outline-success btn-sm">
        <i class="far fa-calendar-plus me-1"></i>
        <span class="d-none d-sm-inline">Kalender</span>
    </a>
    <button class="btn btn-outline-primary btn-sm">
        <i class="far fa-users me-1"></i>
        <span class="d-none d-sm-inline">Aktion</span>
    </button>
</div>
```

- `btn-sm` für kompakte Buttons
- `btn-outline-*` für sekundäre Aktionen
- `d-none d-sm-inline` um Text auf Mobile zu verstecken

---

## Tab-Navigation Pattern

```twig
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1">
                    <i class="far fa-flag me-1"></i>
                    Tab 1
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2">
                    <i class="far fa-list me-1"></i>
                    Tab 2
                    <span class="badge bg-secondary ms-1">5</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab1">...</div>
            <div class="tab-pane fade" id="tab2">...</div>
        </div>
    </div>
</div>
```

---

## Tab-Content Header Pattern

Für Tab-Inhalte mit Überschrift und Aktions-Button:

```twig
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="far fa-info-circle text-muted me-2"></i>
        Überschrift
    </h4>
    <button class="btn btn-outline-primary btn-sm">
        <i class="far fa-plus me-1"></i>Aktion
    </button>
</div>
```

---

## Empty State Pattern

Wenn keine Daten vorhanden sind:

```twig
<div class="text-center py-5">
    <i class="far fa-comments fa-3x text-muted mb-3"></i>
    <h5 class="text-muted">Keine Einträge vorhanden</h5>
    <p class="text-muted mb-0">Beschreibender Text mit Handlungsaufforderung.</p>
</div>
```

Oder als Card:

```twig
<div class="card bg-light border-0">
    <div class="card-body text-center py-5">
        <i class="far fa-map-marker-alt fa-3x text-muted mb-3"></i>
        <h5>Keine Einträge</h5>
        <p class="text-muted mb-3">Beschreibung</p>
        <a href="#" class="btn btn-primary">
            <i class="far fa-plus me-1"></i>Hinzufügen
        </a>
    </div>
</div>
```

---

## Kommentar/Post Pattern

```twig
<div class="card border-0 bg-light mb-3">
    <div class="card-body">
        <div class="d-flex">
            <div class="flex-shrink-0 me-3">
                {# Avatar oder Platzhalter #}
                <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center"
                     style="width: 48px; height: 48px;">
                    <i class="far fa-user text-secondary"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong>Username</strong>
                    <small class="text-muted">
                        <i class="far fa-clock me-1"></i>12.01.2024, 14:30
                    </small>
                </div>
                <div class="content">
                    Inhalt hier...
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## Modal Pattern

```twig
<div class="modal fade" id="my-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="far fa-edit text-primary me-2"></i>Titel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body">
                {# Inhalt #}
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Abbrechen</button>
                <button type="submit" class="btn btn-primary">
                    <i class="far fa-save me-1"></i>Speichern
                </button>
            </div>
        </div>
    </div>
</div>
```

- `modal-dialog-centered` für vertikale Zentrierung
- `border-0` für Header und Footer
- Icon im Titel mit passender Farbe

---

## Inline Social Media Links

```twig
{% if items|length > 0 %}
<div class="mb-3">
    <span class="text-muted me-2">
        <i class="far fa-share-alt me-1"></i>Links:
    </span>
    {% for item in items %}
        <a href="{{ item.url }}" class="btn btn-sm me-1 mb-1"
           style="background-color: {{ item.bgColor }}; color: {{ item.textColor }};"
           target="_blank" rel="noopener">
            <i class="{{ item.icon }} me-1"></i>{{ item.name }}
        </a>
    {% endfor %}
</div>
{% endif %}
```

---

## Responsive Utilities

| Klasse | Bedeutung |
|--------|-----------|
| `d-none d-sm-inline` | Versteckt auf Mobile, sichtbar ab SM |
| `d-none d-md-block` | Versteckt bis MD |
| `col-6 col-md-3` | Halbe Breite Mobile, Viertel ab MD |
| `flex-column flex-md-row` | Spalte auf Mobile, Reihe ab MD |

---

## Icon-Bibliothek

Verwende Font Awesome 5 (Free) mit `far` (regular) Prefix:

| Verwendung | Icon |
|------------|------|
| Datum/Kalender | `fa-calendar-alt`, `fa-calendar-plus` |
| Zeit/Uhr | `fa-clock` |
| Ort/Karte | `fa-map-pin`, `fa-map-marker-alt` |
| Wetter | `fa-cloud-sun` |
| Benutzer | `fa-user`, `fa-users` |
| Bearbeiten | `fa-edit`, `fa-cog` |
| Hinzufügen | `fa-plus` |
| Speichern | `fa-save` |
| Löschen | `fa-trash` |
| Kommentare | `fa-comments`, `fa-comment-alt` |
| Fotos | `fa-images`, `fa-camera` |
| Route/Track | `fa-route`, `fa-bicycle` |
| Teilen | `fa-share-alt` |
| Expand/Compress | `fa-expand`, `fa-compress` |
| Info | `fa-info-circle` |
| Warnung | `fa-exclamation-triangle` |

---

## Stimulus Controller

### Map Expand Controller

Für expandierbare Karten:

```twig
<div data-controller="map-expand" data-map-expand-target="container">
    <button data-action="map-expand#toggle">
        <i class="far fa-expand" data-map-expand-target="icon"></i>
    </button>
</div>
```

CSS-Klassen für expandierten Zustand:
```css
.map-expanded .map-container {
    height: 50vh !important;
    width: 100% !important;
}
```
