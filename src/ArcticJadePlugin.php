<?php

/**
 * Arctic Jade — a crystalline teal ui-theme plugin for Phlix.
 *
 * @copyright 2026 Joe Huss <detain@interserver.net>
 * @license   MIT
 */

declare(strict_types=1);

namespace Phlix\ArcticJade;

use Phlix\Shared\Plugin\LifecycleInterface;
use Phlix\Theming\ThemeSourceInterface;
use Psr\Container\ContainerInterface;

/**
 * Arctic Jade theme plugin — crystalline teal palette on deep ocean-dark surfaces.
 *
 * This theme extends the built-in `midnight` base. The SPA resolves the chain
 * by setting `data-theme="midnight"` and layering these token overrides on top
 * via `el.style.setProperty()`.
 *
 * ## Token philosophy
 *
 * Arctic Jade's accent ramp (`--accent*`) maps the teal spectrum from deep
 * ocean (`--accent`) through luminous jade (`--accent-hover`) to active
 * `--accent-active`. The surface stack steps from near-black (`--bg`) through
 * layered dark-teal surfaces to glass-morphism layers. Text progresses from
 * bright `--text` to progressively muted tones.
 *
 * All values are literals. The host grammar accepts only hex, rgb/rgba/hsl/hsla
 * with numeric arguments, bare numbers, transparent, or currentColor — no
 * `var()`, `url()`, or any other CSS construct.
 *
 * ## Resident-memory contract
 *
 * {@see providedThemes()} returns a literal array. It is called synchronously
 * on the worker thread during plugin enable, so it must not do I/O, must not
 * sleep, and must not memoise into anything that grows.
 *
 * @package Phlix\ArcticJade
 * @since 1.0.0
 */
final class ArcticJadePlugin implements LifecycleInterface, ThemeSourceInterface
{
    /**
     * Canonical provenance key for this source.
     *
     * The host keys the registry's provenance map on this, so re-enabling
     * REPLACES this plugin's themes instead of duplicating them, and disabling
     * removes exactly these ids. Keep it constant across versions.
     */
    public const SOURCE_NAME = 'arctic-jade';

    /**
     * Nothing to do — the host registers the themes off the `instanceof`.
     *
     * @param ContainerInterface $container The host container (unused).
     */
    public function onEnable(ContainerInterface $container): void
    {
    }

    /**
     * Nothing to do — the host deregisters this source by name on disable.
     */
    public function onDisable(): void
    {
    }

    /**
     * A theme plugin subscribes to no events.
     *
     * @return array<class-string, string> Always empty.
     */
    public function subscribedEvents(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function themeSourceName(): string
    {
        return self::SOURCE_NAME;
    }

    /**
     * @inheritDoc
     *
     * @return list<array<array-key, mixed>>
     */
    public function providedThemes(): array
    {
        return [
            [
                'id' => 'arctic-jade',
                'name' => 'Arctic Jade',
                'dark' => true,
                // A BUILT-IN base: only the SPA can resolve this chain.
                'extends' => 'midnight',
                'tokens' => [
                    // Accent ramp — crystalline teal / jade.
                    '--accent' => '#00e5c0',
                    '--accent-hover' => '#33edd2',
                    '--accent-active' => '#00b8a0',
                    '--accent-soft' => 'rgba(0, 229, 192, 0.12)',
                    '--accent-ring' => 'rgba(0, 229, 192, 0.45)',
                    '--accent-text' => '#001f1a',

                    // Background + elevation stack — deep ocean-dark.
                    '--bg' => '#040a09',
                    '--surface' => '#0b1415',
                    '--surface-2' => '#101f21',
                    '--surface-3' => '#162a2d',
                    '--surface-glass' => 'rgba(11, 20, 21, 0.58)',
                    '--surface-glass-strong' => 'rgba(4, 10, 9, 0.82)',

                    // Text ramp — icy whites to muted sea-green.
                    '--text' => '#e5f4f1',
                    '--text-muted' => '#7eb8b3',
                    '--text-subtle' => '#4a7572',
                    '--text-faint' => '#2a4543',
                    '--text-on-accent' => '#001f1a',

                    // Borders.
                    '--border' => '#172c2e',
                    '--border-subtle' => '#0f1f21',
                    '--border-strong' => '#1e3839',

                    // Atmosphere.
                    '--grain-opacity' => '0.035',
                    '--vignette' => 'rgba(0, 0, 0, 0.55)',
                    '--ambient' => 'rgba(0, 229, 192, 0.18)',

                    // Legacy `--color-*` aliases — only the ones the shipped SPA
                    // still reads. See class docblock for the full list of 13
                    // aliases that are deliberately omitted.
                    '--color-bg' => '#040a09',
                    '--color-surface' => '#0b1415',
                    '--color-text' => '#e5f4f1',
                    '--color-text-muted' => '#7eb8b3',
                    '--color-border' => '#172c2e',
                ],
            ],
        ];
    }
}
