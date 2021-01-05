<?php
	namespace Connection;
	
	require_once __DIR__ . '/../index.php';
	
	final class MySQL extends Connection {
		# constructor
		public function __construct(string $host = null, string $user = null, string $password = null, string $database = null, string $port = null) {
			$host = ($host == null) ? \getenv('host') : $host;
			$user = ($user == null) ? \getenv('user') : $user;
			$password = ($password == null) ? \getenv('password') : $password;
			$database = ($database == null) ? \getenv('database') : $database;
			$port = ($port == null) ? (int) \getenv('port') : $port;

			# connect to database or have the value of a error
			$this->connection = new \mysqli($host, $user, $password, $database, $port) or die(\mysqli_error());
			# increase secure
			$this->connection->query("SET SESSION sql_mode = 'NO_BACKSLASH_ESCAPES';");
		}
	
		# closer
		public function close() : void {
			$this->connection->close();
		}
	
		# functions
		# generate function (to generate the SQL code)
		protected function buildQuery(array $args) : string {
			switch ($args[0]) {
				case queryTypes::SELECT:
					$from = $args[1];
					$what = $args[2] ?? '*';
					$where = $args[3] ?? null;

					return ($where == null) ? "SELECT $what FROM `$from`;" : "SELECT $what FROM `$from` WHERE $where;";

					break;
				case queryTypes::COUNT:
					$from = $args[1];
					$what = $args[2];
					$where = $args[3] ?? null;

					return ($where != null) ? "SELECT COUNT($what) FROM `$from` WHERE $where;" : "SELECT COUNT($what) FROM `$from`;";

					break;
				case queryTypes::INSERT:
					$table = $args[1];
					# if the function receive 4 params $what will receive its values, else it'll receive null
					$what = (count($args) == 4) ? $args[2] : null;
					$values = $args[3] ?? $args[2];
					
					return ($what != null) ? "INSERT INTO `$table` ($what) VALUES ($values);" : "INSERT INTO `$table` VALUES ($values);";

					break;
				case queryTypes::UPDATE:
					$from = $args[1];
					$what = $args[2];
					$where = $args[3] ?? null;
					
					return ($where != null) ? "UPDATE `$from` SET $what WHERE $where;" : "UPDATE `$from` SET $what;";

					break;
				case queryTypes::CREATE:
					$table = $args[1];
					$columns = $args[2];
					
					$query = "CREATE TABLE IF NOT EXISTS `$table` (";
					
					$i = 0;
					foreach ($columns as $columm => $value) {
						# when contains 'PRIMARY' replace by MySQL's primary key
						if (strpos($value, ' PRIMARY') !== false) { # for PHP 8: if (str_contains($value, 'PRIMARY'))
							$value = str_replace(' PRIMARY', ' AUTO_INCREMENT PRIMARY KEY', $value);
						}

						if (\is_numeric($columm))
							$query .= "$value";
						else
							$query .= "`$columm` $value";
						
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
					
					return "DROP TABLE IF EXISTS `$table`;";

					break;
				case queryTypes::TRUNCATE:
					$table = $args[1];
					
					return "TRUNCATE TABLE `$table`;";

					break;
				case queryTypes::DELETE:
					$from = $args[1];
					$where = $args[2];

					return "DELETE FROM `$from` WHERE $where;";

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
			$this->connection->query($this->buildQuery([queryTypes::INSERT, $table, $what, $values]));
		}

		public function update(string $from, string $what, string $where = null) : void {
			$this->connection->query($this->buildQuery([queryTypes::UPDATE, $from, $what, $where]));
		}

		public function create(string $table, array $columns) : void {
			$this->connection->query($this->buildQuery([queryTypes::CREATE, $table, $columns]));
		}

		public function drop(string $table) : void {
			$this->connection->query($this->buildQuery([queryTypes::DROP, $table]));
		}

		public function truncate(string $table) : void {
			$this->connection->query($this->buildQuery([queryTypes::TRUNCATE, $table]));
		}

		public function delete(string $table, string $where) : void {
			$this->connection->query($this->buildQuery([queryTypes::DELETE, $table, $where]));
		}

		# SQL functions
		public function nextResult(\mysqli_result $result) {
			return $result->fetch_assoc();
		}
	
		public function numRows(\mysqli_result $result) : int {
			return mysqli_num_rows($result);
		}
	
		public function affectedRows() : int {
			return \mysqli_affected_rows($this->connection);
		}

		# prevent function (to prevent SQL injection)
		public function prevent(string $value) : string {
			return \mysqli_real_escape_string($this->connection, $value);
		}
	}
?>