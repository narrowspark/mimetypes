<?php
declare(strict_types=1);
namespace Narrowspark\MimeType\Build\Command;

use Viserio\Component\Console\Command\AbstractCommand;
use \Mindscreen\YarnLock\YarnLock;

class BuildCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'build';

    /**
     * {@inheritdoc}
     */
    protected $signature = 'build
        [--classname=MimeTypesList : the class name]
        [--namespace=Narrowspark\MimeType : the class namespace]
    ';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Builds the MimeTypesList class';

    /**
     * Path to the stub file.
     *
     * @var string
     */
    private $stubFilePath;

    /**
     * Path to the output place.
     *
     * @var string
     */
    private $outputFilePath;

    /**
     * Path to the mime-db.
     *
     * @var string
     */
    private $mimeDbPath;

    /**
     * A YarnLock instance.
     *
     * @var \Mindscreen\YarnLock\YarnLock
     */
    private $yarnLock;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $rootPath = \dirname(__DIR__, 2);

        $this->stubFilePath   = \dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'stub' . DIRECTORY_SEPARATOR . 'MimetypeClass.stub';
        $this->outputFilePath = $rootPath . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'MimeTypesList.php';
        $this->mimeDbPath     = $rootPath . DIRECTORY_SEPARATOR . 'node_modules' . DIRECTORY_SEPARATOR . 'mime-db' . DIRECTORY_SEPARATOR . 'db.json';
        $this->yarnLock       = YarnLock::fromString((string) \file_get_contents($rootPath . DIRECTORY_SEPARATOR . 'yarn.lock'));
    }

    /**
     * {@inheritdoc}
     */
    public function handle(): int
    {
        $mimeTypeList = self::createMimeArray(\file_get_contents($this->mimeDbPath));

        $this->info('Generating the MimeTypesList class.');

        $output = \file_put_contents(
            $this->outputFilePath,
            \str_replace(
                [
                    '{dummyList}',
                    '{dummyClass}',
                    '{dummyNamespace}',
                    '{date}',
                    '{dummyMimeDbVersion}'
                ],
                [
                    self::getPrettyPrintArray($mimeTypeList),
                    $this->option('classname'),
                    $this->option('namespace'),
                    \gmdate('D, d M Y H:i:s T', time()),
                    $this->yarnLock->getPackage('mime-db')->getVersion()
                ],
                \file_get_contents($this->stubFilePath)
            )
        );

        return (int) ($output !== false);
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