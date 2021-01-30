<?php
	namespace Connection;

	require_once __DIR__ . '/../Operation.php';

	final class Select extends Operation {
		private array $what;
		private array $where = [];
		private int $limit = 0;
		private int $offset = 0;
		private array $orderBy = [];

		public function what(string ...$what) : object {
			$this->what = $what;

			return $this;
		}

		public function where(array | string ...$where) : object {
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

		public function orderBy(array ...$orderBy) : object {
			$this->orderBy = $orderBy;

			return $this;
		}

		private function getOderByPartialQuery() : string {
			$orderBy = '';

			for ($i = 0; $i < count($this->orderBy); $i++) {
				if ($this->name === 'PgSQL') {
					$orderBy .= "{$this->orderBy[$i][0]} ";
				}
				else {
					$orderBy .= "`{$this->orderBy[$i][0]}` ";
				}

				$orderBy .= (count($this->orderBy[$i]) !== 1) ? $this->orderBy[$i][1] : '';

				if ($i < count($this->orderBy) - 1) {
					$orderBy .= ',';
				}
			}

			if ($orderBy !== '') {
				$orderBy = "ORDER BY $orderBy";
			}

			return $orderBy;
		}

		private function select() : array {
			$limit = ($this->limit === 0) ? '' : "LIMIT {$this->limit}";
			$offset = ($this->offset === 0) ? '' : "OFFSET {$this->offset}";
			$orderBy = $this->getOderByPartialQuery();
	
			$what = implode(',', $this->what);

			if ($this->name === 'PgSQL') {
				$query = "SELECT $what FROM {$this->table} $limit $offset $orderBy;";
			}
			else {
				$query = "SELECT $what FROM `{$this->table}` $limit $offset $orderBy;";
			}

			$statement = $this->connection->prepare($query);
			$statement->execute();
			return $statement->fetchAll();
		}

		private function selectWhere() : array {
			$limit = ($this->limit === 0) ? '' : "LIMIT {$this->limit}";
			$offset = ($this->offset === 0) ? '' : "OFFSET {$this->offset}";
			$orderBy = $this->getOderByPartialQuery();

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
				$query = "SELECT $what FROM {$this->table} WHERE $where_question_marks $limit $offset $orderBy;";
			}
			else {
				$query = "SELECT $what FROM `{$this->table}` WHERE $where_question_marks $limit $offset $orderBy;";
			}

			for ($i = 0; $i < count($this->where); $i += 2) {
				$new_array[] = $this->where[$i][2];
			}

			$statement = $this->connection->prepare($query);
			$statement->execute($new_array);

			return $statement->fetchAll();
		}

		public function run() : array {
			if (count($this->where) === 0) {
				$rows = $this->select();
			}
			else {
				$rows = $this->selectWhere();
			}

			foreach ($rows as $row) {
				removeNumericIndexesOfArray($row);
			}

			return $rows;
		}
	}
?>