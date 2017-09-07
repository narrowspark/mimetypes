<?php
declare(strict_types=1);
namespace Narrowspark\Mimetypes;

use Narrowspark\Mimetypes\Exception\AccessDeniedException;
use Narrowspark\Mimetypes\Exception\FileNotFoundException;

final class MimeType
{
    /**
     * Private constructor; non-instantiable.
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Tries to guess the extension.
     *
     * @param string $filename The path to the file
     *
     * @throws \Narrowspark\Mimetypes\Exception\FileNotFoundException If the file does not exist
     * @throws \Narrowspark\Mimetypes\Exception\AccessDeniedException If the file could not be read
     *
     * @return null|string The guessed extension or NULL, if none could be guessed
     */
    public static function guess(string $filename): ?string
    {
        if (! \is_file($filename)) {
            throw new FileNotFoundException($filename);
        }

        if (! \is_readable($filename)) {
            throw new AccessDeniedException($filename);
        }

        $ext = \pathinfo($filename, PATHINFO_EXTENSION);

        return MimeTypeByExtensionGuesser::guess($ext);
    }
}
