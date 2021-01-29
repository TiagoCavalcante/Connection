<?php
	namespace Connection;

	require_once __DIR__ . '/../Operation.php';

	final class Delete extends Operation {
		private array $where;
		private int $limit = 0;

		public function where(array $where) : object {
			$this->where = $where;

			return $this;
		}

		public function limit(int $limit) : object {
			if ($this->name === 'PgSQL') {
				throw new \Exception("PostgreSQL cannot have limit");
			}

			$this->limit = $limit;

			return $this;
		}

		public function run() : void {
			$limit = ($this->limit === 0) ? '' : "LIMIT {$this->limit}";

			$where_question_marks = '';
			for ($i = 0; $i < count($this->where); $i++) {
				if ($i % 2 === 0) {
					if ($this->name === 'PgSQL') {
						$where_question_marks .= "{$this->where[$i][1]} {$this->where[$i][0]} ?";
					}
					else {
						$where_question_marks .= "`{$this->where[$i][1]}` {$this->where[$i][0]} ?";
					}
				}
				else {
					$where_question_marks .= " {$this->where[$i]} ";
				}
			}

			for ($i = 0; $i < count($this->where); $i += 2) {
				$new_array[] = $this->where[$i][2];
			}

			if ($this->name === 'PgSQL') {
				$query = "DELETE FROM {$this->table} WHERE $where_question_marks;";
			}
			else {
				$query = "DELETE FROM `{$this->table}` WHERE $where_question_marks $limit;";
			}

			$this->connection->prepare($query)->execute($new_array);
		}
	}
?>