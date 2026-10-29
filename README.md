# Arctic Jade Theme for Phlix

A crystalline teal UI theme for Phlix with deep ocean-dark surfaces and luminous jade accents.

## Overview

Arctic Jade is a dark theme that extends the built-in `midnight` base. It features a crystalline teal accent palette on deep ocean-dark surfaces, creating a distinctive visual experience with luminous jade highlights.

## Theme Details

- **Theme ID:** arctic-jade
- **Name:** Arctic Jade
- **Extends:** midnight
- **Type:** UI Theme
- **Dark Mode:** Yes

## Color Palette

### Accent Colors

| Token | Value | Description |
|-------|-------|-------------|
| `--accent` | `#00e5c0` | Primary crystalline teal |
| `--accent-hover` | `#33edd2` | Luminous jade hover state |
| `--accent-active` | `#00b8a0` | Active/pressed state |
| `--accent-soft` | `rgba(0, 229, 192, 0.12)` | Soft accent for backgrounds |
| `--accent-ring` | `rgba(0, 229, 192, 0.55)` | Focus ring color |
| `--accent-text` | `#00e5c0` | Accent color as foreground text/icon (AA on bg/surface) |

### Background & Surface

| Token | Value | Description |
|-------|-------|-------------|
| `--bg` | `#040a09` | Page background (near black) |
| `--surface` | `#0b1415` | Primary surface |
| `--surface-2` | `#101f21` | Elevated surface |
| `--surface-3` | `#162a2d` | Higher elevation surface |
| `--surface-glass` | `rgba(11, 20, 21, 0.58)` | Glass morphism base |
| `--surface-glass-strong` | `rgba(4, 10, 9, 0.82)` | Strong glass effect |

### Text Colors

| Token | Value | Description |
|-------|-------|-------------|
| `--text` | `#e5f4f1` | Primary text (icy white) |
| `--text-muted` | `#7eb8b3` | Muted text |
| `--text-subtle` | `#679a95` | Subtle text — placeholders/hints (AA on all surfaces) |
| `--text-faint` | `#2a4543` | Faint/disabled text |
| `--text-on-accent` | `#001f1a` | Text on accent color |

### Borders

| Token | Value | Description |
|-------|-------|-------------|
| `--border` | `#172c2e` | Default border |
| `--border-subtle` | `#0f1f21` | Subtle border |
| `--border-strong` | `#1e3839` | Strong/emphasized border |

### Atmosphere Effects

| Token | Value | Description |
|-------|-------|-------------|
| `--grain-opacity` | `0.035` | Film grain overlay opacity |
| `--vignette` | `rgba(0, 0, 0, 0.55)` | Vignette effect color |
| `--ambient` | `rgba(0, 229, 192, 0.18)` | Ambient glow color |

### Legacy Color Aliases

| Token | Value |
|-------|-------|
| `--color-bg` | `#040a09` |
| `--color-surface` | `#0b1415` |
| `--color-text` | `#e5f4f1` |
| `--color-text-muted` | `#7eb8b3` |
| `--color-border` | `#172c2e` |

## Installation

This package is **not published to Packagist** (its
`repo.packagist.org/p2/detain/phlix-plugin-arctic-jade-theme.json` metadata
endpoint answers 404, verified 2026-10-29), so a plain

```bash
composer require detain/phlix-plugin-arctic-jade-theme
```

resolves nothing — and it was never the install route anyway: the Phlix server
does not scan Composer's `vendor/` for plugins. The plugin loader stages
plugins under the server's `var/plugins/` from digest-pinned sources and
verifies the bytes before extracting anything (remote sources without a pin are
refused by design, not by accident).

The supported route is the official plugin catalog — this repo is listed in
[`detain/phlix-plugins`](https://github.com/detain/phlix-plugins) with a pinned
`ref` + `artifactSha256`:

1. Log in as an admin user.
2. Open **Admin → Plugins** (`/app/admin/plugins`).
3. Find **Arctic Jade Theme** in the catalog and press **Install**.

The CLI equivalent, run from the `phlix-server` install directory, passes the
repository URL and the server supplies the catalog pin itself:

```bash
php bin/phlix plugin:install https://github.com/detain/phlix-plugin-arctic-jade-theme
```

> ⚠ **Redirect-gate caveat, verified 2026-10-29 against a current server:**
> the installer's SSRF gate refuses any response chain containing a `3xx`, and
> GitHub answers every `github.com/…/archive/…` tarball request with a `302`
> to `codeload.github.com`. Until a server-side codeload hop ships, the
> catalog click and the repo-URL form above both fail with
> `HTTP redirects are not allowed`. What works today is a local archive over
> `file://`, which is exempt because the bytes are already operator-local:
>
> ```bash
> # from one directory above your checkout of this repo:
> tar czf /tmp/phlix-plugin-arctic-jade-theme.tar.gz \
>     --exclude='*/vendor' --exclude='*/.git' phlix-plugin-arctic-jade-theme
> php bin/phlix plugin:install file:///tmp/phlix-plugin-arctic-jade-theme.tar.gz
> ```
>
> (Or paste `file:///tmp/phlix-plugin-arctic-jade-theme.tar.gz` into the
> admin **Install from URL** panel.)

Installing is only half the job — see **After installing** below.

## Requirements

- PHP >= 8.3
- Phlix >= 0.44.0

## Development

```bash
# Install dependencies
composer install

# Run PHPStan static analysis (level 9, scans dev-stubs/)
composer phpstan

# Run PHP CodeSniffer (PSR-12 across src/, tests/, dev-stubs/)
composer phpcs

# Run PHPUnit tests
./vendor/bin/phpunit
```

Tests live in `tests/` and load `tests/bootstrap.php`, which registers the
`dev-stubs/` interfaces only when the host contracts are not already present.

CI (`.github/workflows/test.yml`) runs the same checks on PHP `8.3` and `8.4`,
uploading coverage to Codacy from the `8.3` leg:

```bash
vendor/bin/phpunit --colors=always
vendor/bin/phpstan analyse -c phpstan.neon --no-progress
vendor/bin/phpcs --standard=phpcs.xml
```

## License

MIT License - see [LICENSE](LICENSE) for details.

## Author

Joe Huss <detain@interserver.net>
