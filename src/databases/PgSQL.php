<?php
	namespace Connection;

	# requires
	require_once __DIR__ . '/../index.php';
	
	final class PgSQL extends Connection {
		# constructor
		function __construct(string $host = null, string $user = null, string $password = null, string $database = null, string $port = null) {
			$host = ($host == null) ? \getenv('host') : $host;
			$user = ($user == null) ? \getenv('user') : $user;
			$password = ($password == null) ? \getenv('password') : $password;
			$database = ($database == null) ? \getenv('database') : $database;
			$port = ($port == null) ? \getenv('port') : $port;

			$this->connection = new \PDO("pgsql:host=$host;dbname=$database;port=$port;user=$user;password=$password");
		}
	
		# closer
		function close() : void {
			$this->connection = null;
		}
	
		# functions
		# generate function (to generate the SQL code)
		protected function buildQuery(array $args) : string {
			switch ($args[0]) {
				case queryTypes::SELECT:
					$from = $args[1];
					$what = $args[2] ?? '*';
					$where = $args[3] ?? null;
					
					return ($where == null) ? "SELECT $what FROM $from;" : "SELECT $what FROM $from WHERE $where;";

					break;
				case queryTypes::COUNT:
					$from = $args[1];
					$what = $args[2];
					$where = $args[3] ?? null;

					return ($where != null) ? "SELECT COUNT(*) FROM $from WHERE $where;" : "SELECT COUNT(*) FROM $from;";

					break;
				case queryTypes::INSERT:
					$table = $args[1];
					$what = $args[2];
					$values = $args[3];
					
					return "INSERT INTO $table ($what) VALUES ($values);";

					break;
				case queryTypes::UPDATE:
					$from = $args[1];
					$what = $args[2];
					$where = $args[3] ?? null;
					
					return ($where != null) ? "UPDATE $from SET $what WHERE $where;" : "UPDATE $from SET $what;";

					break;
				case queryTypes::CREATE:
					$table = $args[1];
					$columns = $args[2];
					
					$query = "CREATE TABLE IF NOT EXISTS $table (";
					
					$i = 0;
					foreach ($columns as $columm => $value) {
						# when contains 'PRIMARY' replace by PgSQL's primary key
						if (strpos($value, ' PRIMARY') !== false) { # for PHP 8: if (str_contains($value, 'PRIMARY'))
							$value = str_replace(' PRIMARY', ' PRIMARY KEY GENERATED ALWAYS AS IDENTITY', $value);
						}
						
						if (\is_numeric($columm))
							$query .= "$value";
						else
							$query .= "$columm $value";
						
						# if it isn't the last element of the array
						if ($i != count($columns) - 1)
							$query .= ',';

						$i++;
					}
					
					$query .= ');';
					
					return $query;
					
					break;
				case queryTypes::DROP:
					$table = $args[1];
					
					return "DROP TABLE IF EXISTS $table;";

					break;
				case queryTypes::TRUNCATE:
					$table = $args[1];
					
					return "TRUNCATE $table;";

					break;
				case queryTypes::DELETE:
					$from = $args[1];
					$where = $args[2];

					return "DELETE FROM $from WHERE $where;";

					break;
			}
		}
		# query functions
		public function select(string $from, string $what = '*', string $where = null) : object {
			return $this->connection->query($this->buildQuery([queryTypes::SELECT, $from, $what, $where]));
		}
	
		public function count(string $from, string $what = '*', string $where = null) : object {
			return $this->connection->query($this->buildQuery([queryTypes::COUNT, $from, $what, $where]));
		}
	
		public function insert(string $table, string $what, string $values) : void {
			$this->connection->exec($this->buildQuery([queryTypes::INSERT, $table, $what, $values]));
		}

		public function update(string $from, string $what, string $where = null) : void {
			$this->connection->exec($this->buildQuery([queryTypes::UPDATE, $from, $what, $where]));
		}

		public function create(string $table, array $columns) : void {
			$this->connection->exec($this->buildQuery([queryTypes::CREATE, $table, $columns]));
		}

		public function drop(string $table) : void {
			$this->connection->exec($this->buildQuery([queryTypes::DROP, $table]));
		}

		public function truncate(string $table) : void {
			$this->connection->exec($this->buildQuery([queryTypes::TRUNCATE, $table]));
		}

		public function delete(string $table, string $where) : void {
			$this->connection->exec($this->buildQuery([queryTypes::DELETE, $table, $where]));
		}

		# SQL functions
		public function affectedRows(\PDOStatement $result) : int {
			return $result->rowCount();
		}

		# PostgreSQL functions
		public function prepare(string $type) : \PDOStatement {
			return $this->connection->prepare($this->buildQuery(\func_get_args()));
		}
	}
?>