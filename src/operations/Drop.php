<?php
	namespace Connection;

	require_once __DIR__ . '/../Operation.php';

	final class Drop extends Operation {
		public function run() : void {
			if ($this->name == 'PgSQL') {
				$this->connection->exec("DROP TABLE IF EXISTS {$this->table};");
			}
			else {
				$this->connection->exec("DROP TABLE IF EXISTS `{$this->table}`;");
			}
		}
	}
?>