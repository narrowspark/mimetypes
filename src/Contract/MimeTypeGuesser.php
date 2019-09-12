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

namespace Narrowspark\MimeType\Contract;

interface MimeTypeGuesser
{
    /**
     * Returns whether this guesser is supported on the current OS.
     *
     * @return bool
     */
    public static function isSupported(): bool;

    /**
     * Guesses the mime type of the file with the given path.
     *
     * @param string $guess Can be the Filename or File path
     *
     * @throws \Narrowspark\MimeType\Exception\AccessDeniedException If the file could not be read
     * @throws \Narrowspark\MimeType\Exception\FileNotFoundException If the file does not exist
     *
     * @return null|string The mime type or NULL, if none could be guessed
     */
    public static function guess(string $guess): ?string;
}
