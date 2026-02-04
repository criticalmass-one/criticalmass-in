# Claude Code Projekthinweise

## Pull Requests

### Labels
Bei jedem neuen PR automatisch passende Labels setzen:

- **`AI-generated`** - Immer setzen bei von Claude erstellten PRs
- **`PHP`** - Bei Änderungen an PHP-Dateien
- **`Twig`** - Bei Änderungen an Twig-Templates
- **`javascript`** - Bei Änderungen an JavaScript-Dateien
- **`bug`** - Bei Bugfixes
- **`enhancement`** - Bei neuen Features oder Verbesserungen
- **`dependencies`** - Bei Änderungen an composer.json, package.json, yarn.lock

### PR-Erstellung
- Titel kurz und prägnant (unter 70 Zeichen)
- Body mit Summary (Bullet Points) und Test Plan
- Am Ende: `🤖 Generated with [Claude Code](https://claude.com/claude-code)`

## Code-Stil

- PHP: Symfony-Konventionen, strict_types
- Templates: Bootstrap 5
- Tests: PHPUnit

## CI/CD

- PHPStan muss durchlaufen
- PHPUnit Tests müssen bestehen
- Bei PHPStan-Fehlern: phpstan-baseline.neon aktualisieren
