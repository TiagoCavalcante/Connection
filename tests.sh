#!/usr/bin/env bash

# grant permission to execute
chmod +x vendor/bin/phpunit

# autoload test
./vendor/bin/phpunit tests/AutoloadFileTest.php

# create env file
touch env.php

# MySQL test
cat MySQL.env.php > env.php
./vendor/bin/phpunit tests/ConnectionClassTest.php
# PgSQL test
cat PgSQL.env.php > env.php
./vendor/bin/phpunit tests/ConnectionClassTest.php
# SQLite test
cat SQLite.env.php > env.php
./vendor/bin/phpunit tests/ConnectionClassTest.php

# delete env file
rm env.php