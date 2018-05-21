<?php
declare(strict_types=1);
namespace Narrowspark\MimeType\Tests;

use Narrowspark\MimeType\MimeTypeFileInfoGuesser;
use PHPUnit\Framework\TestCase;

class MimeTypeFileInfoGuesserTest extends TestCase
{
    /**
     * @requires extension fileinfo
     */
    public function testIsSupported(): void
    {
        self::assertTrue(MimeTypeFileInfoGuesser::isSupported());
    }

    /**
     * @requires extension fileinfo
     */
    public function testGuessExtensionWithMimeTypeFileInfoGuesser(): void
    {
        self::assertSame(
            'inode/x-empty',
            MimeTypeFileInfoGuesser::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/other-file.example'))
        );

        self::assertSame(
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
