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

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class CommitCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'commit';

    /**
     * {@inheritdoc}
     */
    protected $signature = 'commit';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Commit changes to narrowspark/mimetypes';

    /**
     * {@inheritdoc}
     */
    public function handle(): int
    {
        if ($this->testMimeDbVersion() === 1) {
            return 0;
        }

        $this->info('Making a commit to narrowspark/mimetypes.');

        $gitCommitCommand = 'git commit -m "Automatically updated on ' . (new \DateTimeImmutable('now'))->format(\DateTimeImmutable::RFC7231) . '" -o ' . $this->rootPath . \DIRECTORY_SEPARATOR . 'src' . \DIRECTORY_SEPARATOR . 'MimeTypesList.php  -o ' . $this->rootPath . \DIRECTORY_SEPARATOR . 'package.json';

        $this->info($gitCommitCommand);

        $gitCommitProcess = new Process($gitCommitCommand);
        $gitCommitProcess->run();

        if (! $gitCommitProcess->isSuccessful()) {
            $this->error((new ProcessFailedException($gitCommitProcess))->getMessage());

            return 1;
        }

        $this->info($gitCommitProcess->getOutput());

        $gitPushCommand = 'git push origin HEAD:master --quiet > /dev/null 2>&1';

        $this->info($gitPushCommand);

        $gitPushProcess = new Process($gitPushCommand);
        $gitPushProcess->run();

        if (! $gitPushProcess->isSuccessful()) {
            $this->error((new ProcessFailedException($gitPushProcess))->getMessage());

            return 1;
        }

        $this->info($gitCommitProcess->getOutput());

        $gitGetLastTagCommand = 'git describe --abbrev=0 --tags';

        $this->info($gitGetLastTagCommand);

        $gitGetLastTagProcess = new Process($gitGetLastTagCommand);
        $gitGetLastTagProcess->run();

        if (! $gitGetLastTagProcess->isSuccessful()) {
            $this->error((new ProcessFailedException($gitGetLastTagProcess))->getMessage());

            return 1;
        }

        \preg_match_all('/\.?(\d+)/', \ltrim($gitGetLastTagProcess->getOutput(), 'v'), $result);

        $gitCreateTagCommand = \sprintf('git tag -a %s -m \'%s\'', $result[1][0] . '.' . ($result[1][1] + 1) . '.0', 'updated mime-db to ' . $mimeDbVersion);

        $this->info($gitCreateTagCommand);

        $gitCreateTagProcess = new Process($gitCreateTagCommand);
        $gitCreateTagProcess->run();

        if (! $gitCreateTagProcess->isSuccessful()) {
            $this->error((new ProcessFailedException($gitCreateTagProcess))->getMessage());

            return 1;
        }

        $this->info($tag = $gitCreateTagProcess->getOutput());

        $gitPushTagCommand = \sprintf('git push origin %s --quiet', $tag);

        $this->info($gitPushTagCommand);

        $gitPushTagProcess = new Process($gitPushTagCommand);
        $gitPushTagProcess->run();

        if (! $gitPushTagProcess->isSuccessful()) {
            $this->error((new ProcessFailedException($gitPushTagProcess))->getMessage());

            return 1;
        }

        $this->info($gitPushTagProcess->getOutput());

        return 0;
    }
}
