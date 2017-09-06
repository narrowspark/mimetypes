<?php
declare(strict_types=1);
namespace Narrowspark\Mimetypes\Tests;

use Narrowspark\Mimetypes\Exception\AccessDeniedException;
use Narrowspark\Mimetypes\Exception\FileNotFoundException;
use Narrowspark\Mimetypes\FileinfoMimeTypeGuesser;
use Narrowspark\Mimetypes\MimeType;
use PHPUnit\Framework\TestCase;

class MimeTypeTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass(): void
    {
        if (\file_exists($path = __DIR__ . '/Fixture/to_delete')) {
            @\chmod($path, 0666);
            @\unlink($path);
        }
    }

    public function testGuess(): void
    {
        self::assertEquals(
            'application/vnd.lotus-1-2-3',
            MimeType::guess(__DIR__ . '/Fixture/lotus.123')
        );

        self::assertEquals(
            'application/xml',
            MimeType::guess(__DIR__ . '/Fixture/meta.xml')
        );
    }

    public function testGuessExtensionIsBasedOnMimeType(): void
    {
        self::assertEquals('image/gif', MimeType::guess(__DIR__ . '/Fixture/test'));
    }

    /**
     * @requires extension fileinfo
     */
    public function testGuessExtensionWithFileinfo(): void
    {
        self::assertEquals(
            'inode/x-empty',
            FileinfoMimeTypeGuesser::guess(__DIR__ . '/Fixture/other-file.example')
        );

        self::assertEquals(
            'image/gif',
            FileinfoMimeTypeGuesser::guess(__DIR__ . '/Fixture/test.gif')
        );
    }

    public function testGuessExtensionToThrowExceptionIfNoFileFound(): void
    {
        $path = __DIR__ . '/Fixture/test---';

        try {
            MimeType::guess($path);
        } catch (FileNotFoundException $exception) {
            self::assertSame(\sprintf('The file "%s" does not exist', $path), $exception->getMessage());
        }
    }

    public function testGuessImageWithDirectoryToThrowExceptionIfNoFileFound(): void
    {
        $path = __DIR__ . '/Fixture/directory';

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
            MimeType::guess(__DIR__ . '/Fixture/.unknownextension')
        );
    }

    public function testGuessWithNonReadablePath(): void
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            self::markTestSkipped('Can not verify chmod operations on Windows');
        }

        if (! \getenv('USER') || 'root' === \getenv('USER')) {
            self::markTestSkipped('This test will fail if run under superuser');
        }

        $path = __DIR__ . '/Fixture/to_delete';
        \touch($path);
        @\chmod($path, 0333);

        if (\mb_substr(\sprintf('%o', \fileperms($path)), -4) == '0333') {
            $this->expectException(AccessDeniedException::class);

            MimeType::guess($path);
        } else {
            self::markTestSkipped('Can not verify chmod operations, change of file permissions failed');
        }
    }
}
