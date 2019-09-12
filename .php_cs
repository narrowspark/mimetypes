<?php
use Narrowspark\CS\Config\Config;

$header = <<<'EOF'
This file is part of Narrowspark.

(c) Daniel Bannert <d.bannert@anolilab.de>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

$config = new Config($header, [
    'final_class' => false,
]);
$config->getFinder()
    ->files()
    ->in(__DIR__)
    ->exclude('build/stub')
    ->exclude('vendor')
    ->notPath('tests/Fixture/ActualMimeTypeDbList.php')
    ->notPath('src/MimeTypesList.php')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$cacheDir = getenv('TRAVIS') ? getenv('HOME') . '/.php-cs-fixer' : __DIR__;

$config->setCacheFile($cacheDir . '/.php_cs.cache');

return $config;
