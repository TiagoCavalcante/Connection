#!/usr/bin/env bash

# autoload test
./vendor/bin/phpunit tests/AutoloadFileTest.php

# create env file
touch env.php
# grant permission to execute
chmod 777 vendor/bin/phpunit
# MySQL test
cat MySQL.env.php >> env.php
./vendor/bin/phpunit tests/ConnectionClassTest.php
# PgSQL
cat PgSQL.env.php >> env.php
./vendor/bin/phpunit tests/ConnectionClassTest.php
# SQLite test
cat SQLite.env.php >> env.php
./vendor/bin/phpunit tests/ConnectionClassTest.php
# delete env file
rm env.php