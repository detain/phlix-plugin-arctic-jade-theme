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
| `--accent-ring` | `rgba(0, 229, 192, 0.45)` | Focus ring color |
| `--accent-text` | `#001f1a` | Text on accent backgrounds |

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
| `--text-subtle` | `#4a7572` | Subtle text |
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

```bash
composer require detain/phlix-plugin-arctic-jade-theme
```

## Requirements

- PHP >= 8.3
- Phlix >= 0.44.0

## Development

```bash
# Install dependencies
composer install

# Run PHPStan static analysis
composer phpstan

# Run PHP CodeSniffer
composer phpcs

# Run PHPUnit tests
./vendor/bin/phpunit
```

## License

MIT License - see [LICENSE](LICENSE) for details.

## Author

Joe Huss <detain@interserver.net>
