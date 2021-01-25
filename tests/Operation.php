<?php
	abstract class Operation extends PHPUnit\Framework\TestCase {
		protected object $connection;

		protected function truncate() {
			$this->connection->table('test')
				->truncate()
				->run();
		}

		public function setUp() : void {
			require_once 'env.php';
			require_once 'vendor/autoload.php';

			$this->connection = new Connection\Connection();
			$this->connection->table('test')
				->create()
				->columns([
					'id' => 'INT PRIMARY', 
					'text' => 'TEXT'
				])->run();
		}

		public function tearDown() : void {
			$this->connection->table('test')
				->drop()
				->run();
			$this->connection->close();
		}
	}
?>