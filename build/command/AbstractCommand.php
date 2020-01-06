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

namespace Narrowspark\MimeType\Build\Command;

use Mindscreen\YarnLock\YarnLock;
use Viserio\Component\Console\Command\AbstractCommand as BaseAbstractCommand;
use const DIRECTORY_SEPARATOR;
use function dirname;
use function file_get_contents;
use function json_decode;
use function str_replace;

abstract class AbstractCommand extends BaseAbstractCommand
{
    protected const PACKAGE_JSON_URL = 'https://raw.githubusercontent.com/narrowspark/mimetypes/master/package.json';

    /**
     * Path to dir.
     *
     * @var string
     */
    protected $rootPath;

    /**
     * A YarnLock instance.
     *
     * @var \Mindscreen\YarnLock\YarnLock
     */
    protected $yarnLock;

    /**
     * Test if the current mime db version is older than that was found in yarn lock.
     *
     * @return int
     */
    protected function testMimeDbVersion(): int
    {
        $mimeDbVersion = $this->yarnLock->getPackage('mime-db')->getVersion();
        // Get the last master version to check if the package should be upgraded.
        $masterPackageJson = file_get_contents(static::PACKAGE_JSON_URL);
        $masterPackageArray = json_decode($masterPackageJson, true);

        if ($mimeDbVersion === str_replace('^', '', $masterPackageArray['dependencies']['mime-db'])) {
            $this->info('Nothing to update.');

            return 1;
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->rootPath = dirname(__DIR__, 2);
        $this->yarnLock = YarnLock::fromString((string) file_get_contents($this->rootPath . DIRECTORY_SEPARATOR . 'yarn.lock'));
    }
}
