<?php
declare(strict_types=1);
namespace Narrowspark\Mimetypes\Tests;

use Narrowspark\Mimetypes\FileinfoMimeTypeGuesser;
use PHPUnit\Framework\TestCase;

class FileinfoMimeTypeGuesserTest extends TestCase
{
    /**
     * @requires extension fileinfo
     */
    public function testIsSupported(): void
    {
        self::assertTrue(FileinfoMimeTypeGuesser::isSupported());
    }

    /**
     * @requires extension fileinfo
     */
    public function testGuessExtensionWithFileinfoMimeTypeGuesser(): void
    {
        self::assertSame(
            'inode/x-empty',
            FileinfoMimeTypeGuesser::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/other-file.example'))
        );

        self::assertSame(
            'image/gif',
            FileinfoMimeTypeGuesser::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/test.gif'))
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
