<?php
	final class QueryTest extends Operation {
		public function testCanUseACustomInsert() {
			$this->assertEquals([], $this->connection->query('INSERT INTO test (text) VALUES (?)', 'Hello, world'));
			
			$this->assertEquals(1, $this->connection->table('test')->count()->what('*')->run());

			$this->truncate();
		}

		public function testCanUseACustomSelect() {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what('text')
					->values('Hello, world')
					->run();
			}

			$this->assertCount(10, $this->connection->query('SELECT text FROM test'));

			$this->truncate();
		}
	}
?>