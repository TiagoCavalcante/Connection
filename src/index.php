<?php
	namespace Connection;

	class Connection {
		# constructor
		public function __construct() {
			$name = \getenv('name');
			$host = \getenv('host');
			$user = \getenv('user');
			$password = \getenv('password');
			$database = \getenv('database');
			$port = \getenv('port');

			$this->name = $name;

			switch($name) {
				case 'MySQL':
					$this->connection = new \PDO("mysql:host=$host;dbname=$database;port=$port;user=$user;password=$password");
					# increase security
					$this->connection->query("SET SESSION sql_mode = 'NO_BACKSLASH_ESCAPES';");

					break;
				case 'PgSQL':
					$this->connection = new \PDO("pgsql:host=$host;dbname=$database;port=$port;user=$user;password=$password");

					break;
				case 'SQLite':
					$this->connection = new \PDO("sqlite:$database");

					break;
			}

			# increase security
			$this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		}
	
		# closer
		function close() : void {
			$this->connection = null;
		}

		# query functions
		public function select(string $from, array $what = ['*'], array $where = []) : array {
			$what = implode(',', $what);
			if (count($where) == 0) {
				if ($this->name == 'PgSQL') {
					$query = "SELECT $what FROM $from;";
				}
				else {
					$query = "SELECT $what FROM `$from`;";
				}
				$statement = $this->connection->prepare($query);

				$statement->execute();

				return $statement->fetchAll();
			}
			else {
				# where syntax: [['>', 'id', '0'], 'AND', ['LIKE', 'eye_color', 'blue'], 'OR', ['=', 'eye_color', 'green']]
				$where_question_marks = '';
				for ($i = 0; $i < count($where); $i++) {
					if ($i % 2 == 0) {
						$where_question_marks .= $where[$i][1] . ' ' . $where[$i][0] . ' ?';
					}
					else {
						$where_question_marks .= ' ' . $where[$i];
					}
				}
				
				if ($this->name == 'PgSQL') {
					$query = "SELECT $what FROM $from WHERE $where_question_marks;";
				}
				else {
					$query = "SELECT $what FROM `$from` WHERE $where_question_marks;";
				}
				$statement = $this->connection->prepare($query);

				$new_array = [];
				for ($i = 0; $i < count($where); $i++) {
					if ($i % 2 == 0)
						$new_array[] = $where[$i][2];
				}

				$statement->execute($new_array);

				return $statement->fetchAll();
			}
		}
	
		public function count(string $from, array $what = ['*'], array $where = null) : int {
			$what = implode(',', $what);

			if ($where == null) {
				if ($this->name == 'PgSQL')
					$query = "SELECT COUNT($what) FROM $from;";
				else
					$query = "SELECT COUNT($what) FROM `$from`;";
				$statement = $this->connection->prepare($query);

				$statement->execute();
			}
			else {
				# where syntax: [['>', 'id', '0'], 'AND', ['LIKE', 'eye_color', 'blue'], 'OR', ['=', 'eye_color', 'green']]
				$where_question_marks = '';
				for ($i = 0; $i < count($where); $i++) {
					if ($i % 2 == 0)
						$where_question_marks .= $where[$i][1] . $where[$i][0] . ' ?';
					else
						$where_question_marks .= ' ' . $where[$i];
				}
				
				if ($this->name == 'PgSQL')
					$query = "SELECT COUNT($what) FROM $from WHERE $where_question_marks;";
				else
					$query = "SELECT COUNT($what) FROM `$from` WHERE $where_question_marks;";
				$statement = $this->connection->prepare($query);

				$new_array = [];
				for ($i = 0; $i < count($where); $i++) {
					$new_array[] = $where[$i][2];
				}

				$statement->execute($new_array);
			}
			
			if ($this->name == 'PgSQL')
				return $statement->fetchAll()[0]["count"];
			else
				return $statement->fetchAll()[0]["COUNT($what)"];
		}

		public function insert(string $table, array $what, array $values) : void {
			$what = implode(',', $what);
			$values_question_marks = implode(',', array_fill(0, count($values), '?'));
				
			if ($this->name == 'PgSQL') {
				$query = "INSERT INTO $table ($what) VALUES ($values_question_marks);";
			}
			else {
				$query = "INSERT INTO `$table` ($what) VALUES ($values_question_marks);";
			}
			
			$statement = $this->connection->prepare($query);

			$statement->execute($values);
		}

		public function update(string $from, array $what, array $where = []) : void {
			$what_question_marks = '';
			for ($i = 0; $i < count($what); $i++) {
				$what_question_marks .= $what[$i][1] . $what[$i][0] . ' ?';
			}

			if (count($where) == 0) {
				if ($this->name == 'PgSQL')
					$query = "UPDATE $from SET $what_question_marks;";
				else
					$query = "UPDATE `$from` SET $what_question_marks;";

				$statement = $this->connection->prepare($query);

				$new_array = [];
				for ($i = 0; $i < count($what); $i++) {
					$new_array[] = $what[$i][2];
				}

				$statement->execute($new_array);
			}
			else {
				$where_question_marks = '';
				for ($i = 0; $i < count($where); $i++) {
					if ($i % 2 == 0)
						$where_question_marks .= $where[$i][1] . $where[$i][0] . ' ?';
					else
						$where_question_marks .= ' ' . $where[$i];
				}
	
				if ($this->name == 'PgSQL')
					$query = "UPDATE $from SET $what_question_marks WHERE $where_question_marks;";
				else
					$query = "UPDATE `$from` SET $what_question_marks WHERE $where_question_marks;";
	
				$statement = $this->connection->prepare($query);

				$new_array = [];
				for ($i = 0; $i < count($what); $i++) {
					$new_array[] = $what[$i][2];
				}
				for ($i = 0; $i < count($where); $i++) {
					$new_array[] = $where[$i][2];
				}
	
				$statement->execute($new_array);
			}

		}

		public function create(string $table, array $columns) : void {
			if ($this->name == 'PgSQL')
				$query = "CREATE TABLE IF NOT EXISTS $table (";
			else
				$query = "CREATE TABLE IF NOT EXISTS `$table` (";
						
			$i = 0;
			foreach ($columns as $columm => $value) {
				# when contains 'PRIMARY' replace by MySQL's primary key
				if (strpos($value, ' PRIMARY') !== false) { # for PHP 8: if (str_contains($value, 'PRIMARY'))
					switch ($this->name) {
						case 'MySQL':
							$value = str_replace(' PRIMARY', ' AUTO_INCREMENT PRIMARY KEY', $value);

							break;
						case 'PgSQL':
							$value = str_replace(' PRIMARY', ' PRIMARY KEY GENERATED ALWAYS AS IDENTITY', $value);

							break;
						case 'SQLite':
							$value = str_replace('INT PRIMARY', 'INTEGER PRIMARY KEY AUTOINCREMENT', $value);

							break;
					}
				}

				if (\is_numeric($columm)) {
					$query .= "$value";
				}
				else {
					if ($this->name == 'PgSQL')
						$query .= "$columm $value";
					else
						$query .= "`$columm` $value";
				}
							
				# if it isn't the last element of the array
				if ($i != count($columns) - 1)
					$query .= ',';
								
				$i++;
			}
						
			$query .= ');';

			$this->connection->exec($query);
		}

		public function drop(string $table) : void {
			if ($this->name == 'PgSQL') {
				$this->connection->exec("DROP TABLE IF EXISTS $table;");
			}
			else {
				$this->connection->exec("DROP TABLE IF EXISTS `$table`;");
			}
		}

		public function truncate(string $table) : void {
			if ($this->name == 'PgSQL') {
				$this->connection->exec("TRUNCATE TABLE $table;");
			}
			elseif ($this->name == 'MySQL') {
				$this->connection->exec("TRUNCATE TABLE `$table`;");
			}
			else {
				$this->connection->exec("DELETE FROM `$table`;");
			}
		}

		public function delete(string $table, array $where) : void {
			# where syntax: [['>', 'id', '0'], 'AND', ['LIKE', 'eye_color', 'blue'], 'OR', ['=', 'eye_color', 'green']]
			$where_question_marks = '';
			for ($i = 0; $i < count($where); $i++) {
				if ($i % 2 == 0) {
					if ($this->name == 'PgSQL')
						$where_question_marks .= $where[$i][1] . ' ' . $where[$i][0] . ' ?';
					else
						$where_question_marks .= "`{$where[$i][1]}`" . ' ' . $where[$i][0] . ' ?';
				}
				else {
					$where_question_marks .= ' ' . $where[$i];
				}
			}

			$new_array = [];
			for ($i = 0; $i < count($where); $i++) {
				$new_array[] = $where[$i][2];
			}
			if ($this->name == 'PgSQL') {
				$query = "DELETE FROM $table WHERE $where_question_marks;";
			}
			else {
				$query = "DELETE FROM `$table` WHERE $where_question_marks;";
			}
			
			$statement = $this->connection->prepare($query);
			$statement->execute($new_array);
		}
	}
?>