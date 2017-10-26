#!/usr/bin/env bash

# Create logs dir
mkdir -p build/logs
#!/bin/bash

set +e
bash -e <<TRY
    if [[ "$PHPSTAN" = true ]]; then
        ./vendor/bin/phpstan analyse -c phpstan.neon -l 6 src/
    fi

    if [[ "$CS" = true ]]; then
        ./vendor/bin/php-cs-fixer fix --config=.php_cs --verbose --diff --dry-run
    fi

    if [[ "$SEND_COVERAGE" = true ]]; then
        bash -xc "$TEST -c ./phpunit.xml.dist --verbose --coverage-clover=coverage.xml";
    elif [[ "$PHPUNIT" = true ]]; then
        bash -xc "$TEST -c ./phpunit.xml.dist";
    fi
TRY
if [ $? -ne 0 ]; then
  exit 1
fi
