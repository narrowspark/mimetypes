<?php
declare(strict_types=1);
namespace Narrowspark\MimeType\Tests;

use Mindscreen\YarnLock\YarnLock;
use Narrowspark\MimeType\MimeTypesList;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ActualMimeTypeDbTest extends TestCase
{
    public function testDbIsActual(): void
    {
        $yarnLock = YarnLock::fromString((string) \file_get_contents(\dirname(__DIR__, 1) . \DIRECTORY_SEPARATOR . 'yarn.lock'));

        $this->assertSame($yarnLock->getPackage('mime-db')->getVersion(), MimeTypesList::MIME_DB_VERSION);
    }
}
