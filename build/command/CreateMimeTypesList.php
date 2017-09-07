<?php
declare(strict_types=1);

class CreateMimeTypesList
{
    /**
     * Generate our mime type list.
     *
     * @return int
     */
    public static function create(): int
    {
        [$stubFilePath, $outputFilePath, $mimeDbPath] = static::getFilePaths();

        $mimeTypeList = self::createMimeArray(\file_get_contents($mimeDbPath));

        $output = \file_put_contents(
            $outputFilePath,
            \str_replace(
                [
                    '{dummyList}',
                    '{dummyClass}',
                    '{dummyNamespace}',
                    '{date}'
                ],
                [
                    self::getPrettyPrintArray($mimeTypeList),
                    static::getClassName(),
                    static::getNamespace(),
                    \gmdate('D, d M Y H:i:s T', time())
                ],
                \file_get_contents($stubFilePath)
            )
        );

        return (int) ($output !== false);
    }

    /**
     * @return array
     */
    protected static function getFilePaths(): array
    {
        $rootPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
        $stubFilePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'stub' . DIRECTORY_SEPARATOR . 'MimetypeClass.stub';
        $outputFilePath = $rootPath . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MimeTypesList.php';
        $mimeDbPath = $rootPath . DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR . 'mime-db' . DIRECTORY_SEPARATOR . 'db.json';

        return [$stubFilePath, $outputFilePath, $mimeDbPath];
    }

    /**
     * @return string
     */
    protected static function getClassName(): string
    {
        return 'MimeTypesList';
    }

    /**
     * @return string
     */
    protected static function getNamespace(): string
    {
        return 'Narrowspark\Mimetypes';
    }

    /**
     * @param string $db
     *
     * @return array
     */
    private static function createMimeArray(string $db): array
    {
        $mimeDb = \json_decode($db, true);

        // Map from mime-db to simple mappping "mimetype" => array(ext1, ext2, ext3)
        $mimeDbExtensions = \array_map(
            function ($type) {
                // Format for 'mime-db' is as follow:
                //    "application/xml": {
                //        "source": "iana",
                //        "compressible": true,
                //        "extensions": ["xml","xsl","xsd","rng"]
                //    },
                return $type['extensions'] ?? [];
            },
            \array_values($mimeDb)
        );

        $combinedArray = \ array_combine(\array_keys($mimeDb), $mimeDbExtensions);
        $array = [];

        foreach ($combinedArray as $type => $extensions) {
            foreach ($extensions as $extension) {
                if (!isset($array[$extension])) {
                    $array[$extension] = \array_unique([$type]);
                } else {
                    $array[$extension] = \array_unique(\array_merge($array[$extension], [$type]));
                }
            }
        }

        return $array;
    }

    /**
     * Make php array pretty for save or output.
     *
     * @param array $config
     * @param int   $indentLevel
     *
     * @return string
     */
    private static function getPrettyPrintArray(array $config, int $indentLevel = 1): string
    {
        $indent  = \str_repeat(' ', $indentLevel * 4);
        $entries = [];

        foreach ($config as $key => $value) {
            if (! \is_int($key)) {
                $key = \sprintf("'%s'", $key);
            }

            $entries[] = \sprintf(
                '    %s%s%s,',
                $indent,
                \sprintf('%s => ', $key),
                self::createValue($value, $indentLevel)
            );
        }

        $outerIndent = \str_repeat(' ', ($indentLevel - 1) * 4);

        return \sprintf("[\n%s\n%s    ]", \implode("\n", $entries), $outerIndent);
    }

    /**
     * Create the right value.
     *
     * @param mixed $value
     * @param int   $indentLevel
     *
     * @return string
     */
    private static function createValue($value, int $indentLevel): string
    {
        if (\is_array($value)) {
            return self::getPrettyPrintArray($value, $indentLevel + 1);
        }

        if (\is_numeric($value)) {
            return (string) $value;
        }

        return \var_export($value, true);
    }
}