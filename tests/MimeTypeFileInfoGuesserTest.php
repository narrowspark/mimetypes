<?php
declare(strict_types=1);
namespace Narrowspark\MimeType\Tests;

use Narrowspark\MimeType\MimeTypeFileInfoGuesser;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class MimeTypeFileInfoGuesserTest extends TestCase
{
    /**
     * @requires extension fileinfo
     */
    public function testIsSupported(): void
    {
        static::assertTrue(MimeTypeFileInfoGuesser::isSupported());
    }

    /**
     * @requires extension fileinfo
     */
    public function testGuessExtensionWithMimeTypeFileInfoGuesser(): void
    {
        static::assertSame(
            'inode/x-empty',
            MimeTypeFileInfoGuesser::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/other-file.example'))
        );

        static::assertSame(
            'image/gif',
            MimeTypeFileInfoGuesser::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/test.gif'))
        );
    }

    /**
     * Fix directory separators for windows, linux and normalize path.
     *
     * @param array|string $paths
     *
     * @return array|string
     */
    private static function normalizeDirectorySeparator($paths)
    {
        return \str_replace('\\', '/', $paths);
    }
}
