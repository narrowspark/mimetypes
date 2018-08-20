<?php
declare(strict_types=1);
namespace Narrowspark\MimeType\Tests;

use Narrowspark\MimeType\Exception\AccessDeniedException;
use Narrowspark\MimeType\Exception\FileNotFoundException;
use Narrowspark\MimeType\Exception\RuntimeException;
use Narrowspark\MimeType\MimeType;
use Narrowspark\MimeType\MimeTypeExtensionGuesser;
use Narrowspark\MimeType\MimeTypeFileBinaryGuesser;
use Narrowspark\MimeType\MimeTypeFileInfoGuesser;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class MimeTypeTest extends TestCase
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

    public function testRegister(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('You guesser [Narrowspark\\MimeType\\Tests\\MimeTypeTest] should implement the [\\Narrowspark\\MimeType\\Contract\\MimeTypeGuesser].');

        MimeType::register(self::class);
    }

    public function testGuess(): void
    {
        static::assertSame(
            'application/vnd.lotus-1-2-3',
            MimeType::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/lotus.123'))
        );

        static::assertSame(
            'application/xml',
            MimeType::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/meta.xml'))
        );
    }

    public function testGuessExtensionIsBasedOnMimeType(): void
    {
        $path = self::normalizeDirectorySeparator(__DIR__ . '/Fixture/test');

        static::assertSame('image/gif', MimeTypeFileInfoGuesser::guess($path));

        if (! MimeTypeFileBinaryGuesser::isSupported()) {
            static::assertSame('image/gif', MimeTypeFileBinaryGuesser::guess($path));
        }
    }

    public function testGuessExtensionWithMimeTypeFileBinaryGuesser(): void
    {
        if (! MimeTypeFileBinaryGuesser::isSupported()) {
            static::markTestSkipped('Can only run on a *nix system');
        }

        static::assertSame(
            'application/octet-stream',
            MimeTypeFileBinaryGuesser::guess(self::normalizeDirectorySeparator(__DIR__ . '/Fixture/latlon.bin'))
        );
    }

    public function testGuessExtensionToThrowExceptionIfNoFileFound(): void
    {
        $path = self::normalizeDirectorySeparator(__DIR__ . '/Fixture/test---');

        try {
            MimeType::guess($path);
        } catch (FileNotFoundException $exception) {
            static::assertSame(\sprintf('The file "%s" does not exist.', $path), $exception->getMessage());
        }
    }

    public function testGuessImageWithDirectoryToThrowExceptionIfNoFileFound(): void
    {
        $path = self::normalizeDirectorySeparator(__DIR__ . '/Fixture/directory');

        try {
            MimeType::guess($path);
        } catch (FileNotFoundException $exception) {
            static::assertSame(\sprintf('The file "%s" does not exist.', $path), $exception->getMessage());
        }

        try {
            MimeTypeFileInfoGuesser::guess($path);
        } catch (FileNotFoundException $exception) {
            static::assertSame(\sprintf('The file "%s" does not exist.', $path), $exception->getMessage());
        }
    }

    public function testGuessFileWithUnknownExtension(): void
    {
        $path = self::normalizeDirectorySeparator(__DIR__ . '/Fixture/.unknownextension');

        static::assertSame('application/octet-stream', MimeType::guess($path));
        static::assertSame('application/octet-stream', MimeTypeFileInfoGuesser::guess($path));

        if (! MimeTypeFileBinaryGuesser::isSupported()) {
            static::assertSame('application/octet-stream', MimeTypeFileBinaryGuesser::guess($path));
        }
    }

    public function testGuessWithNonReadablePath(): void
    {
        if (\DIRECTORY_SEPARATOR === '\\') {
            static::markTestSkipped('Can not verify chmod operations on Windows');
        }

        if (! \getenv('USER') || 'root' === \getenv('USER')) {
            static::markTestSkipped('This test will fail if run under superuser');
        }

        $path = self::normalizeDirectorySeparator(__DIR__ . '/Fixture/to_delete');
        \touch($path);
        @\chmod($path, 0333);

        if (\mb_substr(\sprintf('%o', \fileperms($path)), -4) === '0333') {
            $this->expectException(AccessDeniedException::class);

            MimeType::guess($path);
        } else {
            static::markTestSkipped('Can not verify chmod operations, change of file permissions failed');
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
        static::assertSame(
            $mimeType,
            MimeTypeExtensionGuesser::guess($extension)
        );
    }

    /**
     * @return array
     */
    public function extensionDataProvider(): array
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
