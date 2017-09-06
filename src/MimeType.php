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

        $ext       = \pathinfo($filename, PATHINFO_EXTENSION);
        $extension = null;

        if (isset(MimeTypesList::MIMES[$ext])) {
            return MimeTypesList::MIMES[$ext][0];
        }

        if ($extension === null) {
            if (DIRECTORY_SEPARATOR !== '\\' &&
                \function_exists('\passthru') &&
                \function_exists('\escapeshellarg')
            ) {
                return FileBinaryMimeTypeGuesser::guess($filename);
            }

            if (\function_exists('finfo_open')) {
                return FileinfoMimeTypeGuesser::guess($filename);
            }
        }

        return $extension;
    }
}
