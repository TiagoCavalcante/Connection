<?php
	namespace Connection;

	require_once __DIR__ . '/../Operation.php';

	final class Delete extends Operation {
		private array $where;

		public function where(array $where) : object {
			$this->where = $where;

			return $this;
		}

		public function run() : void {
			# where syntax: [['>', 'id', '0'], 'AND', ['LIKE', 'eye_color', 'blue'], 'OR', ['=', 'eye_color', 'green']]
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
				$query = "DELETE FROM `{$this->table}` WHERE $where_question_marks;";
			}

			$this->connection->prepare($query)->execute($new_array);
		}
	}
?>