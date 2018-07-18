<?php
declare(strict_types=1);
namespace Narrowspark\MimeType;

use Narrowspark\MimeType\Contract\MimeTypeGuesser as MimeTypeGuesserContract;
use Narrowspark\MimeType\Exception\AccessDeniedException;
use Narrowspark\MimeType\Exception\FileNotFoundException;

class MimeTypeFileInfoGuesser implements MimeTypeGuesserContract
{
    /**
     * The magic file path.
     *
     * @var null|string
     */
    private static $magicFile;

    /**
     * Private constructor; non-instantiable.
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function isSupported(): bool
    {
        return \function_exists('finfo_open');
    }

    /**
     * A magic file to use with the finfo instance.
     *
     * @param string $magicFile
     */
    public static function addMagicFile(string $magicFile): void
    {
        self::$magicFile = $magicFile;
    }

    /**
     * Guesses the mime type using the PECL extension FileInfo.
     *
     * @param string $filename The path to the file
     *
     * @throws \Narrowspark\MimeType\Exception\FileNotFoundException If the file does not exist
     * @throws \Narrowspark\MimeType\Exception\AccessDeniedException If the file could not be read
     *
     * @return null|string
     *
     * @see http://www.php.net/manual/en/function.finfo-open.php
     */
    public static function guess(string $filename): ?string
    {
        if (! \is_file($filename)) {
            throw new FileNotFoundException($filename);
        }

        if (! \is_readable($filename)) {
            throw new AccessDeniedException($filename);
        }

        if (self::$magicFile !== null) {
            $finfo = \finfo_open(\FILEINFO_MIME_TYPE, self::$magicFile);
        } else {
            $finfo = \finfo_open(\FILEINFO_MIME_TYPE);
        }

        $type = \finfo_file($finfo, $filename);

        \finfo_close($finfo);

        return $type ?? null;
    }
}
