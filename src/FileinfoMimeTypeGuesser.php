<?php
declare(strict_types=1);
namespace Narrowspark\Mimetypes;

use finfo;

class FileinfoMimeTypeGuesser
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
     * Guesses the mime type using the PECL extension FileInfo.
     *
     * @param string $filename The path to the file
     *
     * @return null|string
     */
    public static function guess(string $filename): ?string
    {
        if (! $finfo = new finfo(FILEINFO_MIME_TYPE)) {
            return null;
        }

        return $finfo->file($filename);
    }
}
