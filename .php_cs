<?php

declare(strict_types=1);

use Ergebnis\License;
use Narrowspark\CS\Config\Config;

$license = static function ($path) {
    return License\Type\MIT::markdown(
        $path . '/LICENSE.md',
        License\Range::since(
            License\Year::fromString('2018'),
            new \DateTimeZone('UTC')
        ),
        License\Holder::fromString('Daniel Bannert'),
        License\Url::fromString('https://github.com/narrowspark/automatic')
    );
};

$mainLicense = $license(__DIR__);
$mainLicense->save();

$license(__DIR__ . '/src/Common')->save();
$license(__DIR__ . '/src/LegacyFilter')->save();
$license(__DIR__ . '/src/Security')->save();

$config = new Config($mainLicense->header(), [
    'native_function_invocation' => [
        'exclude' => [
            'getcwd',
            'extension_loaded',
        ],
    ],
    'final_class' => false,
    'final_public_method_for_abstract_class' => false,
]);

$config->getFinder()
    ->files()
    ->in(__DIR__)
    ->exclude('build/stub')
    ->exclude('vendor')
    ->notPath('tests/Fixture/ActualMimeTypeDbList.php')
    ->notPath('src/MimeTypesList.php')
    ->notPath('rector.php')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php_cs.cache');

return $config;
