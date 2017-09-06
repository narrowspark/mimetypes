<?php
declare(strict_types=1);
namespace Narrowspark\Mimetypes\Tests;

use CreateMimeTypesList;
use Narrowspark\Mimetypes\MimeTypesList;
use Narrowspark\Mimetypes\Tests\Fixture\ActualMimeTypeDbList;
use PHPUnit\Framework\TestCase;

class ActualMimeTypeDbTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $create = new class() extends CreateMimeTypesList {
            /**
             * @return array
             */
            protected static function getFilePaths(): array
            {
                [$stubFilePath, $outputFilePath, $mimeDbPath] = parent::getFilePaths();

                $outputFilePath = __DIR__ . '/Fixture/ActualMimeTypeDbList.php';

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
                return 'Narrowspark\\Mimetypes\\Tests\\Fixture';
            }
        };

        self::assertSame(1, $create::create());
    }

    public function testDbIsActual(): void
    {
        self::assertSame(ActualMimeTypeDbList::MIMES, MimeTypesList::MIMES);
    }
}
