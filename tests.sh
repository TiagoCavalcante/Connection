#!/usr/bin/env bash

tests () {
	./vendor/bin/phpunit --stop-on-failure --color tests/operations
}

# grant permission to execute
chmod +x vendor/bin/phpunit

# create env file
touch env.php

# autoload test
./vendor/bin/phpunit --stop-on-failure --color tests/AutoloadTest.php

# MySQL test
cat MySQL.env.php > env.php
tests
# PgSQL test
cat PgSQL.env.php > env.php
tests
# SQLite test
cat SQLite.env.php > env.php
tests

# delete env file
rm env.php