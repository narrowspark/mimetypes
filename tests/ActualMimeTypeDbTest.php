<?php

declare(strict_types=1);

/**
 * This file is part of Narrowspark.
 *
 * (c) Daniel Bannert <d.bannert@anolilab.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Narrowspark\MimeType\Tests;

use Mindscreen\YarnLock\YarnLock;
use Narrowspark\MimeType\MimeTypesList;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @small
 */
final class ActualMimeTypeDbTest extends TestCase
{
    public function testDbIsActual(): void
    {
        $yarnLock = YarnLock::fromString((string) \file_get_contents(\dirname(__DIR__, 1) . \DIRECTORY_SEPARATOR . 'yarn.lock'));

        self::assertSame($yarnLock->getPackage('mime-db')->getVersion(), MimeTypesList::MIME_DB_VERSION);
    }
}
