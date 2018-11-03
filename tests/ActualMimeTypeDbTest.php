<?php
declare(strict_types=1);
namespace Narrowspark\MimeType\Tests;

use CreateMimeTypesList;
use Narrowspark\MimeType\MimeTypesList;
use Narrowspark\MimeType\Tests\Fixture\ActualMimeTypeDbList;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ActualMimeTypeDbTest extends TestCase
{
    public const PATH = __DIR__ . '/Fixture/ActualMimeTypeDbList.php';

    protected function setUp(): void
    {
        parent::setUp();

        $create = new class() extends CreateMimeTypesList {
            /**
             * @return array
             */
            protected static function getFilePaths(): array
            {
                [$stubFilePath, $outputFilePath, $mimeDbPath] = parent::getFilePaths();

                $outputFilePath = ActualMimeTypeDbTest::PATH;

                return [$stubFilePath, $outputFilePath, $mimeDbPath];
            }

            /**
             * @return string
             */
            protected static function getClassName(): string
            {
                return 'ActualMimeTypeDbList';
            }

            /**
             * @return string
             */
            protected static function getNamespace(): string
            {
                return 'Narrowspark\\MimeType\\Tests\\Fixture';
            }
        };

        static::assertSame(1, $create::create());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        \unlink(ActualMimeTypeDbTest::PATH);
    }

    public function testDbIsActual(): void
    {
        $this->assertSame(ActualMimeTypeDbList::MIMES, MimeTypesList::MIMES);
    }
}
