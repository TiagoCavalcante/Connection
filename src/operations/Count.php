<?php
	namespace Connection;

	require_once __DIR__ . '/../Operation.php';

	final class Count extends Operation {
		private array $what;
		private array $where = [];

		public function what(string ...$what) : object {
			$this->what = $what;

			return $this;
		}

		public function where(array | string ...$where) : object {
			$this->where = $where;

			return $this;
		}

		private function count() : int {
			$what = implode(',', $this->what);

			if ($this->name === 'PgSQL') {
				$query = "SELECT COUNT($what) FROM {$this->table};";
			}
			else {
				$query = "SELECT COUNT($what) FROM `{$this->table}`;";
			}

			$statement = $this->connection->prepare($query);
			$statement->execute();

			if ($this->name === 'PgSQL') {
				return $statement->fetchAll()[0]["count"];
			}
			else {
				return $statement->fetchAll()[0]["COUNT($what)"];
			}
		}

		private function countWhere() : int {
			$what = implode(',', $this->what);

			$where_question_marks = '';
			for ($i = 0; $i < count($this->where); $i++) {
				if ($i % 2 === 0) {
					$where_question_marks .= "{$this->where[$i][1]} {$this->where[$i][0]} ?";
				}
				else {
					$where_question_marks .= " {$this->where[$i]} ";
				}
			}

			if ($this->name === 'PgSQL') {
				$query = "SELECT COUNT($what) FROM {$this->table} WHERE $where_question_marks;";
			}
			else {
				$query = "SELECT COUNT($what) FROM `{$this->table}` WHERE $where_question_marks;";
			}

			for ($i = 0; $i < count($this->where); $i += 2) {
				$new_array[] = $this->where[$i][2];
			}

			$statement = $this->connection->prepare($query);
			$statement->execute($new_array);
			
			if ($this->name === 'PgSQL') {
				return $statement->fetchAll()[0]["count"];
			}
			else {
				return $statement->fetchAll()[0]["COUNT($what)"];
			}
		}

		public function run() : int {
			if (count($this->where) === 0) {
				return $this->count();
			}
			else {
				return $this->countWhere();
			}
		}
	}
?>