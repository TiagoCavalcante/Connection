<?php
	namespace Connection;

	require_once __DIR__ . '/../Operation.php';

	final class Create extends Operation {
		private array $columns;

		public function columns(array $columns) : object {
			$this->columns = $columns;

			return $this;
		}

		public function run() : void {
			if ($this->name === 'PgSQL') {
				$query = "CREATE TABLE IF NOT EXISTS {$this->table} (";
			}
			else {
				$query = "CREATE TABLE IF NOT EXISTS `{$this->table}` (";
			}

			$i = 0;
			foreach ($this->columns as $columm => $value) {
				# when contains 'PRIMARY' replace by MySQL's primary key
				# for PHP 8: if (str_contains($value, 'PRIMARY'))
				if (strpos($value, ' PRIMARY') !== false) {
					if ($this->name === 'MySQL') {
						$value = str_replace(' PRIMARY', ' AUTO_INCREMENT PRIMARY KEY', $value);
					}
					elseif ($this->name === 'PgSQL') {
						$value = str_replace(' PRIMARY', ' PRIMARY KEY GENERATED ALWAYS AS IDENTITY', $value);
					}
					else {
						$value = str_replace('INT PRIMARY', 'INTEGER PRIMARY KEY AUTOINCREMENT', $value);
					}
				}

				if (\is_numeric($columm)) {
					$query .= "$value";
				}
				else {
					if ($this->name == 'PgSQL') {
						$query .= "$columm $value";
					}
					else {
						$query .= "`$columm` $value";
					}
				}

				# if it isn't the last element of the array
				if ($i != count($this->columns) - 1) {					
					$query .= ',';
				}

				$i++;
			}

			$query .= ');';

			$this->connection->exec($query);
		}
	}
?>