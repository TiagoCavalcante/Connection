#!/usr/bin/env bash

tests () {
	./vendor/bin/phpunit --stop-on-failure --color tests
}

# grant permission to execute
chmod +x vendor/bin/phpunit

# create env file
touch env.php

# MySQL test
cat MySQL.env.php > env.php
tests

# MariaDB test
cat MariaDB.env.php > env.php
tests

# PgSQL test
cat PgSQL.env.php > env.php
tests

# SQLite test
cat SQLite.env.php > env.php
tests

# delete env file
rm env.php