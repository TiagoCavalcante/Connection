<?php
	namespace Connection;

	require_once __DIR__ . '/../Operation.php';

	final class Select extends Operation {
		private array $what;
		private array $where = [];
		private int $limit = 0;
		private int $offset = 0;

		public function what(array $what) : object {
			$this->what = $what;

			return $this;
		}

		public function where(array $where) : object {
			$this->where = $where;

			return $this;
		}

		public function limit(int $limit) : object {
			$this->limit = $limit;

			return $this;
		}

		public function offset(int $offset) : object {
			$this->$offset = $offset;

			return $this;
		}

		private function select() : array {
			$limit = ($this->limit == 0) ? '' : "LIMIT {$this->limit}";
			$offset = ($this->offset == 0) ? '' : "OFFSET {$this->offset}";
	
			$what = implode(',', $this->what);

			if ($this->name == 'PgSQL') {
				$query = "SELECT $what FROM {$this->table} $limit $offset;";
			}
			else {
				$query = "SELECT $what FROM `{$this->table}` $limit $offset;";
			}

			$statement = $this->connection->prepare($query);
			$statement->execute();
			return $statement->fetchAll();
		}

		private function selectWhere() : array {
			# where syntax: [['>', 'id', '0'], 'AND', ['LIKE', 'eye_color', 'blue'], 'OR', ['=', 'eye_color', 'green']]

			$limit = ($this->limit == 0) ? '' : "LIMIT {$this->limit}";
			$offset = ($this->offset == 0) ? '' : "OFFSET {$this->offset}";

			$what = implode(',', $this->what);

			$where_question_marks = '';
			for ($i = 0; $i < count($this->where); $i++) {
				if ($i % 2 == 0) {
					$where_question_marks .= "{$this->where[$i][1]} {$this->where[$i][0]} ?";
				}
				else {
					$where_question_marks .= " {$this->where[$i]}";
				}
			}

			if ($this->name === 'PgSQL') {
				$query = "SELECT $what FROM {$this->table} WHERE $where_question_marks $limit $offset;";
			}
			else {
				$query = "SELECT $what FROM `{$this->table}` WHERE $where_question_marks $limit $offset;";
			}

			$new_array = [];
			for ($i = 0; $i < count($this->where); $i++) {
				if ($i % 2 == 0) {
					$new_array[] = $this->where[$i][2];
				}
			}

			$statement = $this->connection->prepare($query);
			$statement->execute($new_array);

			return $statement->fetchAll();
		}

		public function run() : array {
			if (count($this->where) == 0) {
				return $this->select();
			}
			else {
				return $this->selectWhere();
			}
		}
	}
?>