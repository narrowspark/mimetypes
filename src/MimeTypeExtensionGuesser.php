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

use Narrowspark\MimeType\Contract\MimeTypeGuesser as MimeTypeGuesserContract;
use function strtolower;

class MimeTypeExtensionGuesser implements MimeTypeGuesserContract
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public static function isSupported(): bool
    {
        return true;
    }

    /**
     * Guesses the mime type from extension.
     *
     * @param string $extension The extension of a file (i.e. 'jpg' or 'uvp')
     *
     * @see http://www.php.net/manual/en/function.finfo-open.php
     *
     * @return null|string
     */
    public static function guess(string $extension): ?string
    {
        $extension = strtolower($extension);

        if (isset(MimeTypesList::MIMES[$extension])) {
            return MimeTypesList::MIMES[$extension][0];
        }

        return null;
    }
}
