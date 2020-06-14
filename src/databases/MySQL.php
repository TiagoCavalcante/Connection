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
		public function generate(SQL $type) : string {
			switch ($type) {
				case SQL::SELECT:
					if (\func_num_args() == 3 || \func_num_args() == 4) {
						$from = \func_get_arg(1);
						$what = \func_get_arg(2);
						# if the 4th param exist $where will receive its values, else it'll receive null
						$where = (\func_num_args() == 4) ? \func_get_arg(3) : null;
						
						return ($where == null) ? "SELECT $what FROM `$from`;" : "SELECT $what FROM `$from` WHERE $where;";
					}
					else {
						throw new Exception('The function expects 3 or 4 parameters but it receives ' . \func_num_args() . ' parameters.');
					}

					break;
				case SQL::COUNT:
					if (\func_num_args() == 3 || \func_num_args() == 4) {
						$from = \func_get_arg(1);
						$what = \func_get_arg(2);
						# if the 3rd parameter exist $where will receive its values, else it'll receive null
						$where = (\func_num_args() == 4) ? \func_get_arg(3) : null;
						
						return ($where != null) ? "SELECT COUNT($what) FROM `$from` WHERE $where;" : "SELECT COUNT($what) FROM `$from`;";
					}
					else {
						throw new Exception('The function expects 3 or 4 parameters but it receives ' . \func_num_args() . ' parameters.');
					}

					break;
				case SQL::INSERT:
					if (\func_num_args() == 3 || \func_num_args() == 4) {
						$table = \func_get_arg(1);
						# if the function receive 4 params $what will receive its values, else it'll receive null
						$what = (\func_num_args() == 4) ? \func_get_arg(2) : null;
						# if the 3rd param exist $values will receive its value, else it'll receive the 2nd param's value
						$values = (\func_num_args() == 3) ? \func_get_arg(2) : \func_get_arg(3);
						
						return ($what != null) ? "INSERT INTO `$table` ($what) VALUES ($values);" : "INSERT INTO `$table` VALUES ($values);";
					}
					else {
						throw new Exception('The function expects 2 or 3 parameters but it receives ' . \func_num_args() . ' parameters.');
					}

					break;
				case SQL::CREATE:
					if (\func_num_args() == 3) {
						$table = \func_get_arg(1);
						$columns = \func_get_arg(2);
						
						return "CREATE TABLE IF NOT EXISTS `$table` ($columns);";
					}
					else {
						throw new Exception('The function expects 2 parameters but it receives ' . \func_num_args() . ' parameters.');
					}

					break;
				case SQL::DROP:
					if (\func_num_args() == 2) {
						$table = \func_get_arg(1);
						
						return "DROP TABLE IF EXISTS `$table`;";
					}
					else {
						throw new Exception('The function expects 1 parameter but it receives ' . \func_num_args() . ' parameters.');
					}

					break;
				case SQL::TRUNCATE:
					if (\func_num_args() == 2) {
						$table = \func_get_arg(1);
						
						return "TRUNCATE TABLE `$table`;";
					}
					else {
						throw new Exception('The function expects 1 parameter but it receives ' . \func_num_args() . ' parameters.');
					}

					break;
				case SQL::DELETE:
					if (\func_num_args() == 2 || \func_num_args() == 3) {
						$from = \func_get_arg(1);
						# if the 2nd param exist $where will receive its value, else it'll receive null
						$where = (\func_num_args() == 3) ? \func_get_arg(2) : null;

						return ($where != null) ? "DELETE FROM `$from` WHERE $where;" : "DELETE FROM `$from`;";
					}
					else {
						throw new Exception('The function expects 1 or 2 parameters but it receives ' . \func_num_args() . ' parameters.');
					}
					break;
			}
		}

		# query functions
		public function select(string $from, string $what = '*', string $where = null) : object {
			return $this->connection->query(generate(SQL::SELECT, "$from", "$what", ($where == null) ? null : "$where"));
		}
	
		public function count(string $from, string $what = '*', string $where = null) : object {
			return $this->connection->query(generate(SQL::COUNT, "$from", "$what", ($where == null) ? null : "$where"));
		}
	
		public function insert(string $table, string $what, string $values) : void {
			$this->connection->query(generate(SQL::INSERT, "$table", "$what", "$values"));
		}

		public function create(string $table, string $columns) : void {
			$this->connection->query(generate(SQL::CREATE, "$table", "$columns"));
		}

		public function drop(string $table) : void {
			$this->connection->query(generate(SQL::DROP, "$table"));
		}

		public function truncate(string $table) : void {
			$this->connection->query(generate(SQL::TRUNCATE, "$table"));
		}

		public function delete(string $table, $where = null) : void {
			$this->connection->query(generate(SQL::DELETE, "$table", ($where == null) ? null : "$where"));
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
			return \mysqli_escape_string($this->mysqli, $value);
		}
	}
?>