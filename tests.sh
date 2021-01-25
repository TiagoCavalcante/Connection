#!/usr/bin/env bash

tests () {
	./vendor/bin/phpunit --stop-on-failure tests/operation/Count.php
	./vendor/bin/phpunit --stop-on-failure tests/operation/Create.php
	./vendor/bin/phpunit --stop-on-failure tests/operation/Delete.php
	./vendor/bin/phpunit --stop-on-failure tests/operation/Drop.php
	./vendor/bin/phpunit --stop-on-failure tests/operation/Insert.php
	./vendor/bin/phpunit --stop-on-failure tests/operation/Select.php
	./vendor/bin/phpunit --stop-on-failure tests/operation/Truncate.php
	./vendor/bin/phpunit --stop-on-failure tests/operation/Update.php
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