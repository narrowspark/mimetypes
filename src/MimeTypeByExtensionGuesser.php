<?php
declare(strict_types=1);
namespace Narrowspark\Mimetypes;

class MimeTypeByExtensionGuesser
{
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
        $extension = \mb_strtolower($extension);

        return isset(MimeTypesList::MIMES[$extension]) ? MimeTypesList::MIMES[$extension][0] : null;
    }
}
