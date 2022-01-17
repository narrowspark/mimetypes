<?php

declare(strict_types=1);

/**
 * This file is part of Narrowspark.
 *
 * (c) Daniel Bannert <d.bannert@anolilab.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Narrowspark\MimeType\Tests;

use Narrowspark\MimeType\MimeTypeFileInfoGuesser;
use PHPUnit\Framework\TestCase;
use const PHP_VERSION_ID;
use function str_replace;

/**
 * @internal
 *
 * @small
 */
final class MimeTypeFileInfoGuesserTest extends TestCase
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
            PHP_VERSION_ID < 70400 ? 'inode/x-empty' : 'application/x-empty',
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
        return str_replace('\\', '/', $paths);
    }
}
