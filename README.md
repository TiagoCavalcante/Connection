# Connection
A secure, multiple database ORM for PHP

## Getting started
First, check if you meet all of the **prerequisites**, then **install**
### Prerequisites
#### MySQL/MariaDB prerequisites
1. Install `MySQL`/`MariaDB` and configure it
2. In the file `php.ini` (if it doesn't exists rename the file `php.ini-development` to `php.ini`) remove the `;` before the `extension=pdo_mysql` (it's probably in the line 939)
#### PostgreSQL prerequisites
1. Install `PostgreSQL` and configure it or crete a `PostgreSQL` database on [Heroku](https://data.heroku.com/)
2. In the file `php.ini` (if it doesn't exists rename the file `php.ini-development` to `php.ini`) remove the `;` before the `extension=pdo_pgsql` (it's probably in the line 942)
### SQLite prerequisites
1. Install `SQLite3` with the following command: `sudo apt-get install sqlite3`
### Instalation
To install you need to:
  * execute the following command to install with Composer: `composer require connection/connection`
  * execute the following command to install with Git: `git clone https://github.com/TiagoCavalcanteTrindade/Connection`
### First sample
`sample.php`:
```php
<?php
	require_once 'env.php';
	require_once 'vendor/autoload.php';

	# initialize the database
	$conn = new Connection\Connection();

	# create the table `posts` with the fields `title` and `text`
	$conn->table('posts')
		->create()
		->columns([
			'title' => 'TEXT',
			'text' => 'TEXT'
		])
		->run();

	for ($i = 0; $i <= 9; $i++) {
		# insert into the table `posts`
		$conn->table('posts')
			->insert()
			->what(['title', 'text'])
			->values(['Title', 'Text'])
			->run();
	}

	# go through the array of results
	foreach ($conn->table('posts')->select()->what(['title', 'text'])->run() as $result) {
		# echo the `title` and the `text` of a post
		echo "Title: {$result['title']}\nText: {$result['text']}\n";
	}

	# colse the connection (necessary for security)
	$conn->close();
?>
```
`env.php`:
```php
<?php
	putenv('name=SQLite');
	putenv('database=database.sqlite3');
?>
```
And to execute:
```bash
php sample.php
```

## Tests
### Before init
Before init you need to:
  * execute the following command: `composer install`
  * add a `env file`:
    * all `envs` need to be defined with `putenv`, e.g.: `putenv('name=SQLite')`
    * all `envs` are case sensitive
    * all `env file` need to have the `env` *name*, its possible values are: `MySQL` (for MySQL and MariaDB), `PgSQL` and `SQLite`
	* all `env file` need to have the `env` *database*
	* the specific `envs` for each database are:
	  * `MySQL`/`MariaDB`: *host*, *port*, *user* and *password*
	  * `PgSQL`: *host*, *port*, *user* and *password*
  * grant perssion to execute `phpunit` with the following command: `chmod 777 vendor/bin/phpunit`
  * grant permission to execute the testing script with the following command: `chmod 777 tests.sh`

### Execute the tests
* Execute all tests:
  * you don't need to edit `env.php` for every database, it's autmated by a script, you just need to create a file named `database.env.php` for each `database`
  * execute the script: `./test.sh`
* Execute `autoload.php` tests:
  * execute the following command: `./vendor/bin/phpunit tests/AutoloadFileTest.php`
* Execute `Connection` tests for specific database:
  * add `env.php` file
  * execute the following command: `./vendor/bin/phpunit tests/ConnectionClassTest.php`

## Documentation
Access the [documentation](https://github.com/TiagoCavalcanteTrindade/Connection/wiki)