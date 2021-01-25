<?php
	namespace Connection;

	require_once __DIR__ . '/../Operation.php';

	final class Insert extends Operation {
		private array $values;
		
		public function what(array $what) : object {
			$this->what = $what;

			return $this;
		}

		public function values(array $values) : object {
			$this->values = $values;

			return $this;
		}

		public function run() : void {
			$what = implode(',', $this->what);
			$values_question_marks = implode(',', array_fill(0, count($this->values), '?'));

			if ($this->name === 'PgSQL') {
				$query = "INSERT INTO {$this->table} ($what) VALUES ($values_question_marks);";
			}
			else {
				$query = "INSERT INTO `{$this->table}` ($what) VALUES ($values_question_marks);";
			}

			$statement = $this->connection->prepare($query);

			$statement->execute($this->values);
		}
	}
?>