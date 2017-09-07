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
     * Returns whether this guesser is supported on the current OS/PHP setup.
     *
     * @return bool
     */
    public static function isSupported()
    {
        return function_exists('finfo_open');
    }

    /**
     * Guesses the mime type using the PECL extension FileInfo.
     *
     * @param string $filename  The path to the file
     * @param string $magicFile A magic file to use with the finfo instance
     *
     * @see http://www.php.net/manual/en/function.finfo-open.php
     *
     * @return null|string
     */
    public static function guess(
        string $filename,
        ?string $magicFile = null
    ): ?string {
        if ($magicFile !== null) {
            $finfo = new finfo(\FILEINFO_MIME_TYPE, $magicFile);
        } else {
            $finfo = new finfo(\FILEINFO_MIME_TYPE);
        }

        if (! $finfo) {
            return null;
        }

        return $finfo->file($filename);
    }
}
