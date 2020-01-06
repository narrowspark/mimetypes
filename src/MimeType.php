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

namespace Narrowspark\MimeType;

use LogicException;
use Narrowspark\MimeType\Contract\MimeTypeGuesser as MimeTypeGuesserContract;
use Narrowspark\MimeType\Exception\RuntimeException;
use RuntimeException as BaseRuntimeException;
use function array_unshift;
use function class_implements;
use function count;
use function in_array;
use function sprintf;

final class MimeType
{
    /**
     * All registered MimeTypeGuesserInterface instances.
     *
     * @var string[]
     */
    private static $guessers = [];

    /**
     * Check for the native guessers if they a registered.
     *
     * @var bool
     */
    private static $nativeGuessersLoaded = false;

    /**
     * Private constructor; non-instantiable.
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Register all natively provided mime type guessers.
     *
     * @return string[]
     */
    private static function getGuessers(): array
    {
        if (! self::$nativeGuessersLoaded) {
            if (MimeTypeFileExtensionGuesser::isSupported()) {
                self::$guessers[] = MimeTypeFileExtensionGuesser::class;
            }

            if (MimeTypeFileInfoGuesser::isSupported()) {
                self::$guessers[] = MimeTypeFileInfoGuesser::class;
            }

            if (MimeTypeFileBinaryGuesser::isSupported()) {
                self::$guessers[] = MimeTypeFileBinaryGuesser::class;
            }

            self::$nativeGuessersLoaded = true;
        }

        return self::$guessers;
    }

    /**
     * Registers a new mime type guesser.
     * When guessing, this guesser is preferred over previously registered ones.
     *
     * @param string $guesser Should implement \Narrowspark\MimeType\Contract\MimeTypeGuesser interface
     *
     * @return void
     */
    public static function register(string $guesser): void
    {
        if (in_array(MimeTypeGuesserContract::class, class_implements($guesser), true)) {
            array_unshift(self::$guessers, $guesser);
        }

        throw new RuntimeException(sprintf('You guesser [%s] should implement the [\%s].', $guesser, MimeTypeGuesserContract::class));
    }

    /**
     * Tries to guess the mime type.
     *
     * @param string $guess The path to the file or the file extension
     *
     * @throws \Narrowspark\MimeType\Exception\FileNotFoundException If the file does not exist
     * @throws \Narrowspark\MimeType\Exception\AccessDeniedException If the file could not be read
     *
     * @return null|string The guessed extension or NULL, if none could be guessed
     */
    public static function guess(string $guess): ?string
    {
        $guessers = self::getGuessers();

        if (count($guessers) === 0) {
            $msg = 'Unable to guess the mime type as no guessers are available';

            if (! MimeTypeFileInfoGuesser::isSupported()) {
                $msg .= ' (Did you enable the php_fileinfo extension?).';
            }

            throw new LogicException($msg);
        }

        $exception = null;

        foreach ($guessers as $guesser) {
            try {
                $mimeType = $guesser::guess($guess);
            } catch (BaseRuntimeException $e) {
                $exception = $e;

                continue;
            }

            if ($mimeType !== null) {
                return $mimeType;
            }
        }

        // Throw the last catched exception.
        if ($exception !== null) {
            throw $exception;
        }

        return null;
    }
}
