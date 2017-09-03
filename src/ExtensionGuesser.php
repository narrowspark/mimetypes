<?php
declare(strict_types=1);
namespace Narrowspark\Mimetypes;

use finfo;
use Narrowspark\Mimetypes\Exception\AccessDeniedException;
use Narrowspark\Mimetypes\Exception\FileNotFoundException;

final class ExtensionGuesser
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
     * Registers a new extension guesser.
     *
     * When guessing, this guesser is preferred over previously registered ones.
     *
     * @param callable $guesser
     *
     * @return void
     */
    public static function register(callable $guesser): void
    {
        \array_unshift(self::$guessers, $guesser);
    }

    /**
     * Clear all guessers.
     *
     * @return void
     */
    public static function flush(): void
    {
        self::$guessers = [];
    }

    /**
     * Guesses the mime type with the binary "file" (only available on *nix).
     *
     * @param string $path
     * @param string $cmd  The command to run to get the mime type of a file.
     *                     The $cmd pattern must contain a "%s" string that will be replaced
     *                     with the file name to guess.
     *                     The command output must start with the mime type of the file.
     *
     * @return null|string
     */
    public static function getFileBinaryMimeTypeGuess(
        string $path,
        string $cmd = 'file -b --mime %s 2>/dev/null'
    ): ?string {
        \ob_start();

        // need to use --mime instead of -i. see #6641
        \passthru(\sprintf($cmd, \escapeshellarg($path)), $return);

        if ($return > 0) {
            \ob_end_clean();

            return null;
        }

        $type = \trim(\ob_get_clean());

        if (! \preg_match('#^([a-z0-9\-]+/[a-z0-9\-\.]+)#i', $type, $match)) {
            // it's not a type, but an error message
            return null;
        }

        return $match[1];
    }

    /**
     * Guesses the mime type using the PECL extension FileInfo.
     *
     * @param string $path
     *
     * @return null|string
     */
    public static function getFileinfoMimeTypeGuess(string $path): ?string
    {
        if (! $finfo = new finfo(FILEINFO_MIME_TYPE)) {
            return null;
        }

        return $finfo->file($path);
    }

    /**
     * Tries to guess the extension.
     *
     * The path is passed to each registered mime type guesser in reverse order
     * of their registration (last registered is queried first). Once a guesser
     * returns a value that is not NULL, this method terminates and returns the
     * value.
     *
     * @param string $path The path to the file
     *
     * @throws \Narrowspark\Mimetypes\Exception\FileNotFoundException If the file does not exist
     * @throws \Narrowspark\Mimetypes\Exception\AccessDeniedException If the file could not be read
     *
     * @return null|string The guessed extension or NULL, if none could be guessed
     */
    public static function guess(string $path): ?string
    {
        if (! \is_file($path)) {
            throw new FileNotFoundException($path);
        }

        if (! \is_readable($path)) {
            throw new AccessDeniedException($path);
        }
    }
}
