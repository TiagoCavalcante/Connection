<?php
	namespace Connection;

	# requires
	require_once __DIR__ . '/../Connection.php';
	
	final class SQLite extends Connection {
		# constructor
		function __construct(string $database = null) {
			$database = ($database == null) ? \getenv('database') : $database;

			$this->connection = new \SQLite3($database);
		}
	
		# closer
		function close() : void {
			$this->connection->close();
		}
	
		# functions
		# generate function (to generate the SQL code)
		protected function buildQuery(string $type) : string {
			switch ($type) {
				case queryTypes::SELECT:
					$from = \func_get_arg(1);
					$what = \func_get_args()[2] ?? '*';
					$where = \func_get_args()[3] ?? null;
					
					return ($where == null) ? "SELECT $what FROM `$from`;" : "SELECT $what FROM `$from` WHERE $where;";

					break;
				case queryTypes::COUNT:
					$from = \func_get_arg(1);
					$where = \func_get_args()[2] ?? null;
					
					return ($where != null) ? "SELECT COUNT(*) FROM `$from` WHERE $where;" : "SELECT COUNT(*) FROM `$from`;";

					break;
				case queryTypes::INSERT:
					$table = \func_get_arg(1);
					$what = \func_get_arg(2);
					$values = \func_get_arg(3);
					
					return "INSERT INTO `$table` ($what) VALUES ($values);";

					break;
				case queryTypes::UPDATE:
					$from = \func_get_arg(1);
					$what = \func_get_arg(2);
					$where = \func_get_args()[3] ?? null;
					
					return ($where != null) ? "UPDATE `$from` SET $what WHERE $where;" : "UPDATE `$from` SET $what;";

					break;
				case queryTypes::CREATE:
					$table = \func_get_arg(1);
					$columns = \func_get_arg(2);
					
					$query = "CREATE TABLE IF NOT EXISTS `$table` (";
					
					$i = 0;
					foreach ($columns as $columm => $value) {
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
					$table = \func_get_arg(1);
					
					return "DROP TABLE IF EXISTS `$table`;";

					break;
				case queryTypes::TRUNCATE:
					$table = \func_get_arg(1);
					
					return "DELETE FROM `$table`;";

					break;
				case queryTypes::DELETE:
					$from = \func_get_arg(1);
					$where = \func_get_arg(2);

					return "DELETE FROM `$from` WHERE $where;";

					break;
			}
		}
		# query functions
		public function select(string $from, string $what = '*', string $where = null) : object {
			return $this->connection->query($this->buildQuery(queryTypes::SELECT, $from, $what, $where));
		}
	
		public function count(string $from, string $what = '*', string $where = null) : object {
			return $this->connection->query($this->buildQuery(queryTypes::COUNT, $from, $where));
		}
	
		public function insert(string $table, string $what, string $values) : void {
			$this->connection->exec($this->buildQuery(queryTypes::INSERT, $table, $what, $values));
		}

		public function update(string $from, string $what, string $where = null) : void {
			$this->connection->exec($this->buildQuery(queryTypes::UPDATE, $from, $what, $where));
		}

		public function create(string $table, array $columns) : void {
			$this->connection->exec($this->buildQuery(queryTypes::CREATE, $table, $columns));
		}

		public function drop(string $table) : void {
			$this->connection->exec($this->buildQuery(queryTypes::DROP, $table));
		}

		public function truncate(string $table) : void {
			$this->connection->exec($this->buildQuery(queryTypes::TRUNCATE, $table));
		}

		public function delete(string $table, string $where) : void {
			$this->connection->exec($this->buildQuery(queryTypes::DELETE, $table, $where));
		}
	
		# SQL functions
		public function nextResult(\SQLite3Result $result) {
			return $result->fetchArray();
		}
	
		public function affectedRows() : int {
			return $this->connection->changes();
		}

		# SQLite functions
		public function prepare(string $type) : \SQLite3Stmt {
			return $this->connection->prepare($this->buildQuery($type, \func_get_arg(1), \func_get_args()[2] ?? null, \func_get_args()[3] ?? null));
		}
	}
?>