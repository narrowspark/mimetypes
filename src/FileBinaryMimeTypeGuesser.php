<?php
declare(strict_types=1);
namespace Narrowspark\Mimetypes;

class FileBinaryMimeTypeGuesser
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
    public static function guess(
        string $path,
        string $cmd = 'file -b --mime %s 2>/dev/null'
    ): ?string {
        \ob_start();

        // need to use --mime instead of -i.
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
}
