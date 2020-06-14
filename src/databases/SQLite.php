<?php
	namespace Connection;

	# requires
	require_once __DIR__ . '/../Connection.php';
	
	final class SQLite extends Connection {
		# constructor
		function __construct(string $database = DATABASE) {
			# connect to database or have the value of a error
			$this->connection = new \SQLite3($database);
		}
	
		# closer
		function close() : void {
			$this->connection->close();
		}
	
		# functions
		# query functions
		function select(string $from, string $what = '*', string $where = null) : object {
			return ($where != null) ? $this->connection->query("SELECT $what FROM $from WHERE $where;") : $this->connection->query("SELECT $what FROM $from");
		}
	
		function count(string $from, string $what = '*', string $where = null) : object {
			return ($where != null) ? $this->connection->query("SELECT COUNT($what) FROM $from WHERE $where;") : $this->connection->query("SELECT COUNT($what) FROM $from;");
		}
	
		function insert(string $table, string $what, string $values) : void {
			$this->connection->exec("INSERT INTO $table ($what) VALUES ($values);");
		}

		public function create(string $table, string $columns) : void {
			$this->connection->exec("CREATE TABLE IF NOT EXISTS `$table` ($columns);");
		}

		public function drop(string $table) : void {
			$this->connection->exec("DROP TABLE IF EXISTS $table;");
		}

		public function truncate(string $table) : void {
			$this->connection->exec("DELETE FROM $table;");
		}
	
		# SQL functions
		function nextResult($result) {
			return (gettype($result) != 'boolean') ? $result->fetchArray() : false;
		}
	
		function numRows($result) : int {
			return (gettype($result) != 'boolean') ? \mysqli_num_rows($result) : false;
		}
	
		function affectedRows() : int {
			return $this->connection->changes();
		}

		# SQLite functions
		function prepare(SQL $type) {
			switch ($type) {
				case SQL::SELECT:
					# code...
					break;
				case SQL::COUNT:
					break;
				case SQL::INSERT:
					break;
				case SQL::INSERT:
					break;
				case SQL::INSERT:
					break;
				case SQL::INSERT:
					break;	
			}
		}
	}
?>