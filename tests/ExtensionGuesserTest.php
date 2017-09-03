<?php
declare(strict_types=1);
namespace Narrowspark\Mimetypes\Tests;

use PHPUnit\Framework\TestCase;
use Narrowspark\Mimetypes\Exception\AccessDeniedException;
use Narrowspark\Mimetypes\ExtensionGuesser;

class ExtensionGuesserTest extends TestCase
{
    public static function tearDownAfterClass(): void
    {
        $path = __DIR__ . '/Fixture/to_delete';

        if (\file_exists($path)) {
            @\chmod($path, 0666);
            @\unlink($path);
        }
    }

    public function testGuessExtensionIsBasedOnMimeType(): void
    {
        self::assertEquals('gif', ExtensionGuesser::guess(__DIR__ . '/Fixture/test'));
    }

    /**
     * @requires extension fileinfo
     */
    public function testGuessExtensionWithFileinfo(): void
    {
        self::assertEquals(
            'inode/x-empty',
            ExtensionGuesser::getFileinfoMimeTypeGuess(__DIR__ . '/Fixture/other-file.example')
        );

        self::assertEquals(
            'image/gif',
            ExtensionGuesser::getFileinfoMimeTypeGuess(__DIR__ . '/Fixture/test.gif')
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to guess the mime type as no guessers are available.
     */
    public function testGuessWithIncorrectPath(): void
    {
        ExtensionGuesser::guess(__DIR__ . '/Fixture/test.gif');
    }

    /**
     * @expectedException \Narrowspark\Mimetypes\Exception\FileNotFoundException
     */
    public function testGuessExtensionToThrowExceptionIfNoFileFound(): void
    {
        ExtensionGuesser::guess(__DIR__ . '/Fixture/test---');
    }

    public function testGuessFileWithUnknownExtension(): void
    {
        self::assertEquals(
            'application/octet-stream',
            ExtensionGuesser::guess(__DIR__ . '/Fixture/.unknownextension')
        );
    }

    public function testGuessWithNonReadablePath(): void
    {
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Can not verify chmod operations on Windows');
        }

        if (! \getenv('USER') || 'root' === \getenv('USER')) {
            $this->markTestSkipped('This test will fail if run under superuser');
        }

        $path = __DIR__ . '/Fixture/to_delete';
        \touch($path);
        @\chmod($path, 0333);

        if (\mb_substr(\sprintf('%o', \fileperms($path)), -4) == '0333') {
            $this->expectException(AccessDeniedException::class);

            ExtensionGuesser::guess($path);
        } else {
            $this->markTestSkipped('Can not verify chmod operations, change of file permissions failed');
        }
    }
}
