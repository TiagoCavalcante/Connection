<?php
	namespace Connection;
	
	require_once __DIR__ . '/../Connection.php';
	
	final class MySQL extends Connection {
		# constructor
		public function __construct(string $host = HOST, string $user = USER, string $password = PASSWORD, string $database = DATABASE) {
			# connect to database or have the value of a error
			$this->connection = new \mysqli($host, $user, $password, $database) or die(\mysqli_error());
		}
	
		# closer
		public function close() : void {
			$this->connection->close();
		}
	
		# functions
		# generate function (to generate the SQL code)
		protected function buildQuery(string $type) : string {
			switch ($type) {
				case queryTypes::SELECT:
					if (\func_num_args() == 3 || \func_num_args() == 4) {
						$from = \func_get_arg(1);
						$what = \func_get_arg(2);
						# if the 4th param exist $where will receive its values, else it'll receive null
						$where = (\func_num_args() == 4) ? \func_get_arg(3) : null;
						
						return ($where == null) ? "SELECT $what FROM `$from`;" : "SELECT $what FROM `$from` WHERE $where;";
					}
					else {
						throw new \Exception('The function expects 3 or 4 params but it receives ' . \func_num_args());
					}

					break;
				case queryTypes::COUNT:
					if (\func_num_args() == 3 || \func_num_args() == 4) {
						$from = \func_get_arg(1);
						$what = \func_get_arg(2);
						# if the 3rd parameter exist $where will receive its values, else it'll receive null
						$where = (\func_num_args() == 4) ? \func_get_arg(3) : null;
						
						return ($where != null) ? "SELECT COUNT($what) FROM `$from` WHERE $where;" : "SELECT COUNT($what) FROM `$from`;";
					}
					else {
						throw new \Exception('The function expects 3 or 4 params but it receives ' . \func_num_args());
					}

					break;
				case queryTypes::INSERT:
					if (\func_num_args() == 3 || \func_num_args() == 4) {
						$table = \func_get_arg(1);
						# if the function receive 4 params $what will receive its values, else it'll receive null
						$what = (\func_num_args() == 4) ? \func_get_arg(2) : null;
						# if the 3rd param exist $values will receive its value, else it'll receive the 2nd param's value
						$values = (\func_num_args() == 3) ? \func_get_arg(2) : \func_get_arg(3);
						
						return ($what != null) ? "INSERT INTO `$table` ($what) VALUES ($values);" : "INSERT INTO `$table` VALUES ($values);";
					}
					else {
						throw new \Exception('The function expects 3 or 4 params but it receives ' . \func_num_args());
					}

					break;
				case queryTypes::CREATE:
					if (\func_num_args() == 3) {
						$table = \func_get_arg(1);
						$columns = \func_get_arg(2);
						
						return "CREATE TABLE IF NOT EXISTS `$table` ($columns);";
					}
					else {
						throw new \Exception('The function expects 3 params but it receives ' . \func_num_args());
					}

					break;
				case queryTypes::DROP:
					if (\func_num_args() == 2) {
						$table = \func_get_arg(1);
						
						return "DROP TABLE IF EXISTS `$table`;";
					}
					else {
						throw new \Exception('The function expects 2 params but it receives ' . \func_num_args());
					}

					break;
				case queryTypes::TRUNCATE:
					if (\func_num_args() == 2) {
						$table = \func_get_arg(1);
						
						return "TRUNCATE TABLE `$table`;";
					}
					else {
						throw new \Exception('The function expects 2 params but it receives ' . \func_num_args());
					}

					break;
				case queryTypes::DELETE:
					if (\func_num_args() == 3) {
						$from = \func_get_arg(1);
						$where = \func_get_arg(2);

						return "DELETE FROM `$from` WHERE $where;";
					}
					else {
						throw new \Exception('The function expects 3 params but it receives ' . \func_num_args());
					}

					break;
			}
		}

		# query functions
		public function select(string $from, string $what = '*', string $where = null) : object {
			return $this->connection->query($this->buildQuery(queryTypes::SELECT, "$from", "$what", ($where == null) ? null : "$where"));
		}
	
		public function count(string $from, string $what = '*', string $where = null) : object {
			return $this->connection->query($this->buildQuery(queryTypes::COUNT, "$from", "$what", ($where == null) ? null : "$where"));
		}
	
		public function insert(string $table, string $what, string $values) : void {
			$this->connection->query($this->buildQuery(queryTypes::INSERT, "$table", "$what", "$values"));
		}

		public function create(string $table, string $columns) : void {
			$this->connection->query($this->buildQuery(queryTypes::CREATE, "$table", "$columns"));
		}

		public function drop(string $table) : void {
			$this->connection->query($this->buildQuery(queryTypes::DROP, "$table"));
		}

		public function truncate(string $table) : void {
			$this->connection->query($this->buildQuery(queryTypes::TRUNCATE, "$table"));
		}

		public function delete(string $table, string $where) : void {
			$this->connection->query($this->buildQuery(queryTypes::DELETE, "$table", "$where"));
		}

		# SQL functions
		public function nextResult(\mysqli_result $result) {
			return (gettype($result) != 'boolean') ? $result->fetch_assoc() : false;
		}
	
		public function numRows(\mysqli_result $result) : int {
			return (gettype($result) != 'boolean') ? mysqli_num_rows($result) : false;
		}
	
		public function affectedRows() : int {
			return \mysqli_affected_rows($this->connection);
		}

		# prevent function (to prevent SQL injection)
		public function prevent(string $value) : string {
			return \mysqli_escape_string($this->connection, $value);
		}
	}
?>