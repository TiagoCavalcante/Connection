<?php
	namespace Connection;

	require_once __DIR__ . '/Table.php';

	final class Connection {
		public function __construct() {
			$name = \getenv('name');
			$host = \getenv('host');
			$user = \getenv('user');
			$password = \getenv('password');
			$database = \getenv('database');
			$port = \getenv('port');

			$this->name = $name;

			if ($name === 'MySQL') {
				$this->connection = new \PDO("mysql:host=$host;dbname=$database;port=$port;user=$user;password=$password");
				# increase security
				$this->connection->query("SET SESSION sql_mode = 'NO_BACKSLASH_ESCAPES';");
			}
			elseif ($name === 'PgSQL') {
				$this->connection = new \PDO("pgsql:host=$host;dbname=$database;port=$port;user=$user;password=$password");
			}
			elseif ($name === 'SQLite') {
				$this->connection = new \PDO("sqlite:$database");
			}
			else {
				throw new \Exception('the env "name" must be "MySQL" | "PgSQL" | "SQLite"');
			}

			$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			# increase security
			$this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		}

		function close() : void {
			$this->connection = null;
		}

		function table(string $table) : object {
			return new Table($this->connection, $this->name, $table);
		}

		function query(string $query, ...$arguments) : array {
			$statement = $this->connection->prepare("$query;");
			$statement->execute($arguments);

			$rows = $statement->fetchAll();

			// SQLite3 returns [0 => []] for non-queries (insert, delete, ...)
			if ($rows === [[]]) {
				$rows = [];
			}

			return $rows;
		}
	}
?>