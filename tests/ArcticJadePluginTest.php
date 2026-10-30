<?php

declare(strict_types=1);

namespace Phlix\ArcticJade\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Phlix\ArcticJade\ArcticJadePlugin;

#[CoversClass(ArcticJadePlugin::class)]
final class ArcticJadePluginTest extends TestCase
{
    /**
     * The 53 token names the host's fail-closed allowlist accepts.
     *
     * Local copy of `Phlix\Theming\ThemeTokenAllowlist::all()`
     * (phlix-server, src/Theming/ThemeTokenAllowlist.php), itself transcribed
     * from the theme blocks of `@phlix/tokens/src/css/colors.css`. Transcribed
     * at the 2026-10-30 server tip; `ThemeSourceRegistry::register()` matches
     * keys against this exact, case-sensitive set with `in_array(..., true)`,
     * so any name outside it hard-fails the whole theme enable.
     *
     * @var list<string>
     */
    private const HOST_TOKEN_ALLOWLIST = [
        // ACCENT
        '--accent',
        '--accent-hover',
        '--accent-active',
        '--accent-soft',
        '--accent-ring',
        '--accent-text',
        // SURFACE
        '--bg',
        '--surface',
        '--surface-2',
        '--surface-3',
        '--surface-glass',
        '--surface-glass-strong',
        // TEXT
        '--text',
        '--text-muted',
        '--text-subtle',
        '--text-faint',
        '--text-on-accent',
        // BORDER
        '--border',
        '--border-subtle',
        '--border-strong',
        // STATUS
        '--error',
        '--error-bg',
        '--success',
        '--success-bg',
        '--warning',
        '--warning-bg',
        '--info',
        '--info-bg',
        // ATMOSPHERE
        '--grain-opacity',
        '--vignette',
        '--ambient',
        // LEGACY_ALIAS (22)
        '--color-bg',
        '--color-surface',
        '--color-surface-hover',
        '--color-surface-elevated',
        '--color-surface-active',
        '--color-text',
        '--color-text-secondary',
        '--color-text-muted',
        '--color-text-subtle',
        '--color-primary',
        '--color-primary-hover',
        '--color-primary-active',
        '--color-border',
        '--color-border-subtle',
        '--color-error',
        '--color-error-bg',
        '--color-success',
        '--color-success-bg',
        '--color-warning',
        '--color-warning-bg',
        '--color-info',
        '--color-info-bg',
    ];

    /** Longest accepted token value — the server's MAX_VALUE_LENGTH. */
    private const MAX_VALUE_LENGTH = 128;

    /** Bare colour keywords accepted by the host, compared lowercased. */
    private const HOST_VALUE_KEYWORDS = ['transparent', 'currentcolor'];

    /** `#rgb`, `#rgba`, `#rrggbb`, `#rrggbbaa` — the server's HEX_PATTERN. */
    private const HEX_PATTERN =
        '/^#(?:[0-9A-Fa-f]{3}|[0-9A-Fa-f]{4}|[0-9A-Fa-f]{6}|[0-9A-Fa-f]{8})\z/';

    /** A bare number (`0`, `0.05`, `.5`, `-0.2`) — the server's NUMBER_PATTERN. */
    private const NUMBER_PATTERN = '/^[+-]?(?:[0-9]+(?:\.[0-9]+)?|\.[0-9]+)\z/';

    private ArcticJadePlugin $plugin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->plugin = new ArcticJadePlugin();
    }

    #[Test]
    public function testImplementsLifecycleInterface(): void
    {
        $this->assertInstanceOf(\Phlix\Shared\Plugin\LifecycleInterface::class, $this->plugin);
    }

    #[Test]
    public function testImplementsThemeSourceInterface(): void
    {
        $this->assertInstanceOf(\Phlix\Theming\ThemeSourceInterface::class, $this->plugin);
    }

    #[Test]
    public function testThemeSourceNameReturnsCorrectValue(): void
    {
        $this->assertSame('arctic-jade', $this->plugin->themeSourceName());
        $this->assertSame('arctic-jade', ArcticJadePlugin::SOURCE_NAME);
    }

    #[Test]
    public function testProvidedThemesReturnsExpectedStructure(): void
    {
        $themes = $this->plugin->providedThemes();

        // providedThemes() is typed list<array<array-key, mixed>>; the
        // structural law below is what this test actually pins.
        $this->assertNotEmpty($themes, 'Plugin must provide at least one theme');

        $theme = $themes[0];
        $this->assertSame('arctic-jade', $theme['id']);
        $this->assertSame('Arctic Jade', $theme['name']);
        $this->assertTrue($theme['dark']);
        $this->assertSame('midnight', $theme['extends']);
        $this->assertArrayHasKey('tokens', $theme);
        $this->assertIsArray($theme['tokens']);
    }

    #[Test]
    public function testEveryTokenNameIsOnTheHostAllowlist(): void
    {
        $tokens = $this->tokensOfShippedTheme();

        foreach ($tokens as $token => $value) {
            $this->assertContains(
                (string) $token,
                self::HOST_TOKEN_ALLOWLIST,
                sprintf(
                    'Token "%s" is not one of the 53 names the host accepts; '
                    . 'ThemeSourceRegistry::register() would fail the whole enable.',
                    (string) $token
                )
            );
        }
    }

    #[Test]
    public function testAllTokensAreValidCssValues(): void
    {
        $tokens = $this->tokensOfShippedTheme();

        foreach ($tokens as $token => $value) {
            $this->assertIsString($value, "Token {$token} must have a string value");
            $this->assertNotEmpty($value, "Token {$token} must not be empty");
            // Verify NO var() references - all values must be literal CSS values
            $this->assertStringNotContainsString('var(', $value, "Token {$token} must not contain var() references");
            $this->assertStringNotContainsString('url(', $value, "Token {$token} must not contain url() references");
            // The host grammar, not a substring denylist: ThemeTokenValidator
            // fails the ENTIRE enable on any value outside these four narrow
            // productions, so the palette must live inside them.
            $this->assertTrue(
                self::hostGrammarAccepts($value),
                sprintf(
                    'Token "%s" value "%s" does not match the host grammar '
                    . '(#hex, rgb()/rgba()/hsl()/hsla() comma/slash-separated numerics, '
                    . 'bare number, transparent, currentColor; <=%d chars).',
                    (string) $token,
                    $value,
                    self::MAX_VALUE_LENGTH
                )
            );
        }
    }

    #[Test]
    public function testGrammarMirrorCarriesTeethOnTheKnownEscapeForms(): void
    {
        // Negative legs pinned against the mirror itself: if the regex is
        // ever loosened, this test reddens before the palette can ship an
        // enable-killing value green (the space-separated rgb() form is the
        // exact value class the weak pre-W-min126 test let through).
        foreach (self::grammarRejections() as $rejected) {
            $this->assertFalse(
                self::hostGrammarAccepts($rejected),
                sprintf('Grammar mirror must reject "%s" exactly as the host does', $rejected)
            );
        }
    }

    /**
     * Value forms the host grammar refuses — each one green under the old
     * substring-only test, each one a hard enable-failure in production.
     *
     * @return list<string>
     */
    private static function grammarRejections(): array
    {
        return [
            'rgb(0 229 192 / 12%)',
            '#fff}body{background:url(evil.example)}',
            'var(--bg)',
            'url(x)',
            'calc(1px + 2em)',
            '#12345',
            'rgb(1, 2, 3, 4, 5)',
            str_repeat('0', self::MAX_VALUE_LENGTH + 1),
            'expression(alert(1))',
            'transparent; color: red',
        ];
    }

    /**
     * @return array<array-key, mixed>
     */
    private function tokensOfShippedTheme(): array
    {
        $themes = $this->plugin->providedThemes();
        $theme = $themes[0];
        $tokens = $theme['tokens'];
        $this->assertIsArray($tokens, 'Theme payload must carry a tokens map');

        return $tokens;
    }

    /**
     * Whether a token VALUE satisfies the host grammar.
     *
     * Mirror of `Phlix\Theming\ThemeTokenValidator::isSafeValue()`
     * (phlix-server, src/Theming/ThemeTokenValidator.php) — same four full-
     * string-anchored productions, same \z end anchor (a `$` would accept a
     * trailing newline), same literal-space-only interior whitespace, same
     * 128-char cap, same lowercased keyword set. Kept as a local copy because
     * the validator lives in the server package, not in this plugin's
     * autoload graph; the negative test above pins the mirror's shape.
     */
    private static function hostGrammarAccepts(string $value): bool
    {
        $trimmed = trim($value);

        if ($trimmed === '' || strlen($trimmed) > self::MAX_VALUE_LENGTH) {
            return false;
        }

        if (in_array(strtolower($trimmed), self::HOST_VALUE_KEYWORDS, true)) {
            return true;
        }

        return preg_match(self::HEX_PATTERN, $trimmed) === 1
            || preg_match(self::functionPattern(), $trimmed) === 1
            || preg_match(self::NUMBER_PATTERN, $trimmed) === 1;
    }

    /**
     * The server's FUNCTION_PATTERN assembled from its ARGUMENT production.
     */
    private static function functionPattern(): string
    {
        $argument = '[+-]?(?:[0-9]+(?:\.[0-9]+)?|\.[0-9]+)(?:%|(?i:deg))?';

        return '/^(?i:rgba?|hsla?)\((?:[ ]*)'
            . $argument
            . '(?:(?:[ ]*)[,\/](?:[ ]*)' . $argument . '){2,3}(?:[ ]*)\)\z/';
    }

    #[Test]
    public function testOnEnableDoesNotThrow(): void
    {
        $container = $this->createMock(\Psr\Container\ContainerInterface::class);

        // The contract is the absence of a throw: reaching the next line
        // IS the assertion. addToAssertionCount keeps PHPUnit's risky-test
        // gate satisfied without a tautological assertTrue.
        $this->plugin->onEnable($container);
        $this->addToAssertionCount(1);
    }

    #[Test]
    public function testOnDisableDoesNotThrow(): void
    {
        // No-throw contract, same law as onEnable above.
        $this->plugin->onDisable();
        $this->addToAssertionCount(1);
    }
}
