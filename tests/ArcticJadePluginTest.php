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
    public function testAllTokensAreValidCssValues(): void
    {
        $themes = $this->plugin->providedThemes();
        $theme = $themes[0];
        $tokens = $theme['tokens'];
        $this->assertIsArray($tokens, 'Theme payload must carry a tokens map');

        foreach ($tokens as $token => $value) {
            $this->assertIsString($value, "Token {$token} must have a string value");
            $this->assertNotEmpty($value, "Token {$token} must not be empty");
            // Verify NO var() references - all values must be literal CSS values
            $this->assertStringNotContainsString('var(', $value, "Token {$token} must not contain var() references");
            $this->assertStringNotContainsString('url(', $value, "Token {$token} must not contain url() references");
        }
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
