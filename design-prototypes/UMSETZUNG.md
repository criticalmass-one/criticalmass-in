# Umsetzungsplan: Redesign „Aktivität" (06-Serie)

Referenz-Prototypen (untereinander verlinkt, responsive verifiziert bei 1440 px und 390 px):

| Seite | Datei | Status |
|---|---|---|
| Timeline (Startseite) | `06-feed-aktiv.html` | ✔ Prototyp |
| Tourseite | `06a-tour.html` | ✔ Prototyp |
| Stadtseite | `06b-stadt.html` | ✔ Prototyp |
| Kalender | `06c-kalender.html` | ✔ Prototyp (mobil: Agenda statt Raster) |
| Profil & Konto | `06d-profil.html` | ✔ Prototyp |
| Karte / Entdecken | `06e-karte.html` | ✔ Prototyp (mobil: Bottom-Sheet) |
| Forum | `06f-forum.html` | ✔ Prototyp |
| Foto-Galerie | `06g-galerie.html` | ✔ Prototyp |
| Hochladen (Tracks/Fotos/Schätzung) | `06h-hochladen.html` | ✔ Prototyp |
| Statistik | `06i-statistik.html` | ✔ Prototyp |
| Suche | `06j-suche.html` | ✔ Prototyp |
| Anmelden | `06k-anmelden.html` | ✔ Prototyp |
| Registrieren (2 Schritte) | `06o-registrieren.html` | ✔ Prototyp |
| Städte-Verzeichnis | `06l-verzeichnis.html` | ✔ Prototyp |
| Mitteilungen | `06m-mitteilungen.html` | ✔ Prototyp |
| Tour eintragen (Formular) | `06n-tour-anlegen.html` | ✔ Prototyp |

Damit ist **jede Hauptseite der Plattform** als Prototyp im 06-System vorhanden.
Die Abschnitte unten dokumentieren die Gestaltungsentscheidungen.

## Design-Tokens

| Token | Wert | Anmerkung |
|---|---|---|
| Akzent (`$primary`) | `#f4581c` | Warnwesten-Orange, Hover `#d8450e` |
| Tinte / Text | `#16181c` | statt Bootstrap-Schwarz |
| Hintergrund | `#f2f3f5` | kühles Grau, Karten weiß |
| Linien | `#e3e5e8` | 1px-Borders statt Schatten |
| Grün (Erfolg/gefahren) | `#1e7d4f` | |
| Schrift Fließtext | Archivo (400–800) | |
| Schrift Zahlen/Daten | IBM Plex Mono (500/600) | Statistikbänder, Zeiten, Countdown |
| Radius | 14 px Karten, 9 px Buttons, 99 px Pills | |

**Fonts unbedingt selbst hosten** (DSGVO — kein Google-Fonts-CDN):
`yarn add @fontsource/archivo @fontsource/ibm-plex-mono`, Import in `assets/app.js`.

## Wiederkehrende Komponenten (aus den Prototypen extrahierbar)

`.activity` (Feed-Karte) · `.statband`/`.statrow` (Mono-Kennzahlen) · `.kudosbar` + „Klingeln"
(Glocken-Icon statt Like) · `.ava`/`.facepile` (Initialen-Avatare) · `.kbtn` (Button-Familie) ·
`.tabs` (Segmented Control) · `.uprow` („Bald unterwegs"-Zeile) · `.datebadge` ·
`.rankrow` (Städte-Ranking mit Balken) · Tabbar mobil (fixed bottom).

Die CSS-Klassen in den Prototypen sind bewusst so geschrieben, dass sie 1:1 als
SCSS-Partials übernommen werden können.

## Designentscheidungen der Seiten 06i–06n

Alle aus denselben Bausteinen, kein neues Vokabular nötig:

- **Statistik** (`templates/Statistic/`): Seitenkopf + großes `.statband`, darunter
  Chart.js-Liniendiagramm (Teilnehmende über Zeit, Akzent-Orange auf Grau) und das
  `.rankrow`-Ranking (Top 10 wie in der Feed-Rail, nur länger). Stadtstatistik =
  Monats-Balken aus `06b` in groß. Tabellen mit `font-feature-settings: "tnum"`.
- **Suche** (`templates/Search/`): Großes Suchfeld als `.float-search`-Pill, Ergebnisse in
  zwei Tabs (Städte | Touren) als `.crow`-Zeilen aus `06e` (Treffer-Begriff fett markiert).
  Mobil identisch, eine Spalte.
- **Anmelden / Registrieren** (`templates/Security/`): Eine zentrierte Karte (max. 420 px)
  auf `--bg`, Logo-Mark oben, große Eingabefelder im `.compose input`-Stil,
  Primärbutton `.kbtn.solid`. Registrierung in 2 Schritten (E-Mail → Name/Passwort),
  FriendlyCaptcha unauffällig unterm Button. Keine Ablenkung, kein Rail.
- **Städte-Verzeichnis / Regionen** (`templates/Region/`): Kontinente als `.tabs`,
  darunter Regionen-Karten im `.board`-Stil aus `06f` (Initial-Icon, Name, Städtezahl),
  Städte als `.crow`-Liste. Alternativ direkt auf `06e-karte` verweisen — Karte und
  Verzeichnis sind dieselbe Datenbasis.
- **Mitteilungen**: Dropdown unter dem Topbar-Glocken-Icon + eigene Seite als schmale
  `.thread`-Liste (Avatar, Text, Zeit, ungelesen = Orange-Punkt). Typen: geklingelt,
  geantwortet, Tour in deiner Stadt angelegt, Track zugeordnet.
- **Formulare (Tour/Stadt anlegen & bearbeiten)**: `.box` mit Abschnitts-Überschriften,
  Felder gruppiert wie die `dl`-Zeilen der Tourseite; Karten-Picker (bestehender
  `map--form-map`-Controller) bekommt den 14-px-Radius-Rahmen. „Drei Felder genügen"-Promise:
  Pflicht sind nur Stadt, Datum, Uhrzeit — Rest einklappbar („Mehr Details").

## Schritte / PR-Schnitt

> **Stand 13.06.2026:** PR 1 = [#1406](https://github.com/criticalmass-one/criticalmass-in/pull/1406) (offen) ·
> PR 2 = [#1407](https://github.com/criticalmass-one/criticalmass-in/pull/1407) (offen, gestapelt) ·
> Arbeitspakete als Issues: PR 3 → #1408 · PR 4 → #1409 · PR 5 → #1410 · PR 6 → #1411 ·
> PR 7 → #1412 · PR 8 → #1413 · PR 9 → #1414 · PR 10 → #1415 · PR 11 → #1416 ·
> Übersicht/Epic: #1417

1. **PR 1 — Fundament (rein additiv, bricht nichts):**
   Branch `feature/redesign-activity`. Fonts via @fontsource; Bootstrap-Variablen in
   `assets/scss/bootstrap5.scss` überschreiben (`$primary`, `$font-family-base`,
   `$border-radius`, Grautöne); neues Verzeichnis `assets/scss/redesign/` mit
   `_tokens.scss` + `_components.scss` (Komponenten von oben).
2. **PR 2 — Chrome:** Navbar (`templates/Template/Navigation/`) zur Topbar des Prototyps
   (Logo-Mark, Suche, „Hochladen"-Button, Avatar), Footer verschlanken. Buttons/Karten
   wirken durch PR 1 bereits neu.
3. **PR 3 — Startseite = Timeline:** `templates/Frontpage/index.html.twig` auf
   Feed-Layout (Hauptspalte + Rail) umbauen; Timeline-Item-Templates
   (`templates/Timeline/`) als `.activity`-Karten mit Statistikband; „Bald unterwegs"
   als `.uprow`-Widget; dunkle „Nächste Masse"-Leiste oben.
4. **PR 4 — Tourseite** (`templates/Ride/`): Hero-Karte mit Treffpunkt-Map (bestehender
   `map--ride-map`-Controller bleibt!), Dabei-Leiste, Statistikband, Tabs wie gehabt.
5. **PR 5 — Stadtseite** (`templates/City/`): Cover, Folgen-Button (neues Feature oder
   vorerst weglassen), Stadt-Feed, Monats-Chart (Chart.js vorhanden).
6. **PR 6 — Kalender** (`templates/Calendar/`): Desktop-Raster restylen, mobil
   Agenda-Liste ergänzen (`_calendar.scss` ersetzen).
7. **PR 7 — Profil** (`templates/ProfileManagement/`): Kartenstapel → Profilseite.
8. **PR 8 — Karte/Entdecken** (`templates/Explore/`): Panel-Layout aus `06e`,
   bestehender `map--explore-map`-Controller bleibt; mobil Bottom-Sheet.
9. **PR 9 — Forum** (`templates/Board/`): Board-Karten + Thread-Zeilen aus `06f`.
10. **PR 10 — Galerie & Hochladen** (`templates/PhotoGallery/`, `templates/Track/`):
    Masonry-Galerie aus `06g` (CSS columns), Upload-Flow aus `06h` auf den bestehenden
    Dropzone-/TrackDecider-Unterbau.
11. **PR 11 — Rest:** Statistik, Suche, Anmelden, Verzeichnis, Mitteilungen, Formulare
    nach den Designvorgaben oben; danach Bootstrap-3-Kompat-Layer entfernen.

## Nicht anfassen

Leaflet/MapLibre-Stimulus-Controller, Custom-Entity-Router, Datenmodell, API.
FontAwesome Pro vorerst behalten (Inline-SVGs der Prototypen sind optional, kein Muss).

## Bekannte Stolpersteine

- Bootstrap-3-Kompat-Layer (`.panel`, `.well` …) wird noch von Alt-Templates genutzt —
  erst nach PR 3–7 entfernen.
- `$primary`-Wechsel färbt sofort alle Links/Buttons — gewollt, aber einmal komplett
  durchklicken (v. a. Formulare, Pagination, Modals).
- „Klingeln" braucht Backend (neue Entity ähnlich Like/Kudos) — fürs Erste kann die
  Kudosbar ohne Funktion gerendert oder weggelassen werden.
- Neue Features in den Prototypen, die es noch nicht gibt: Städten folgen,
  Spoke-Card-Abzeichen, Live-Status. Alles optional, Design funktioniert ohne.
