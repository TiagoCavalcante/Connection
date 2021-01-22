<?php
	namespace Connection;

	abstract class Operation {
		protected object $connection;
		protected string $name;
		protected string $table;

		public function __construct(object $connection, string $name, string $table) {
			$this->connection = $connection;
			$this->name = $name;
			$this->table = $table;
		}
	}
?>