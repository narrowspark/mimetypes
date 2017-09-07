<?php
declare(strict_types=1);
namespace Narrowspark\Mimetypes\Tests;

use Narrowspark\Mimetypes\Exception\AccessDeniedException;
use Narrowspark\Mimetypes\Exception\FileNotFoundException;
use Narrowspark\Mimetypes\FileBinaryMimeTypeGuesser;
use Narrowspark\Mimetypes\FileinfoMimeTypeGuesser;
use Narrowspark\Mimetypes\MimeType;
use Narrowspark\Mimetypes\MimeTypeByExtensionGuesser;
use PHPUnit\Framework\TestCase;

class MimeTypeTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass(): void
    {
        if (\file_exists($path = self::normalizeDirectorySeparator(__DIR__ . '/Fixture/to_delete'))) {
            @\chmod($path, 0666);
            @\unlink($path);
        }
    }

    public function testGuess(): void
    {
        self::assertEquals(
            'application/vnd.lotus-1-2-3',
            MimeType::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/lotus.123'))
        );

        self::assertEquals(
            'application/xml',
            MimeType::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/meta.xml'))
        );
    }

    public function testGuessExtensionIsBasedOnMimeType(): void
    {
        self::assertEquals('image/gif', MimeType::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/test')));
    }

    /**
     * @requires extension fileinfo
     */
    public function testGuessExtensionWithFileinfoMimeTypeGuesser(): void
    {
        self::assertEquals(
            'inode/x-empty',
            FileinfoMimeTypeGuesser::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/other-file.example'))
        );

        self::assertEquals(
            'image/gif',
            FileinfoMimeTypeGuesser::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/test.gif'))
        );
    }

    public function testGuessExtensionWithFileBinaryMimeTypeGuesser(): void
    {
        if (FileBinaryMimeTypeGuesser::isSupported()) {
            self::markTestSkipped('Can only run on a nix* system');
        }

        self::assertEquals(
            'application/octet-stream',
            FileBinaryMimeTypeGuesser::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/latlon.bin'))
        );
    }

    public function testGuessExtensionToThrowExceptionIfNoFileFound(): void
    {
        $path = self::normalizeDirectorySeparator(__DIR__ . '/Fixture/test---');

        try {
            MimeType::guess($path);
        } catch (FileNotFoundException $exception) {
            self::assertSame(\sprintf('The file "%s" does not exist', $path), $exception->getMessage());
        }
    }

    public function testGuessImageWithDirectoryToThrowExceptionIfNoFileFound(): void
    {
        $path = self::normalizeDirectorySeparator(__DIR__ . '/Fixture/directory');

        try {
            MimeType::guess($path);
        } catch (FileNotFoundException $exception) {
            self::assertSame(\sprintf('The file "%s" does not exist', $path), $exception->getMessage());
        }
    }

    public function testGuessFileWithUnknownExtension(): void
    {
        self::assertEquals(
            'application/octet-stream',
            MimeType::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/.unknownextension'))
        );
    }

    public function testGuessWithNonReadablePath(): void
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            self::markTestSkipped('Can not verify chmod operations on Windows');
        }

        if (! \getenv('USER') || 'root' === \getenv('USER')) {
            self::markTestSkipped('This test will fail if run under superuser');
        }

        $path = self::normalizeDirectorySeparator(__DIR__ . '/Fixture/to_delete');
        \touch($path);
        @\chmod($path, 0333);

        if (\mb_substr(\sprintf('%o', \fileperms($path)), -4) == '0333') {
            $this->expectException(AccessDeniedException::class);

            MimeType::guess($path);
        } else {
            self::markTestSkipped('Can not verify chmod operations, change of file permissions failed');
        }
    }

    /**
     * @dataProvider extensionDataProvider
     *
     * @param string      $extension
     * @param null|string $mimeType
     */
    public function testGuessMimeTypeFromExtension(string $extension, ?string $mimeType): void
    {
        self::assertEquals(
            $mimeType,
            MimeTypeByExtensionGuesser::guess($extension)
        );
    }

    /**
     * @return array
     */
    public function extensionDataProvider()
    {
        return [
            ['jpg', 'image/jpeg'],
            ['wmz', 'application/x-ms-wmz'],
            ['ecelp9600', 'audio/vnd.nuera.ecelp9600'],
            ['unknownextension', null],
        ];
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
