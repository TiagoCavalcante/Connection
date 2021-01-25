<?php
	final class InsertTest extends Operation {
		public function testCanDoAInsert() : void {
			$this->assertEquals(
				null,
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run()
			);

			$this->truncate();
		}

		public function testCanDoAInsertWithoutSQLInjection() : void {
			$this->connection->table('test')
				->insert()
				->what(['text'])
				->values(['\'; DROP TABLE `test`; -- '])
				->run();

			foreach ($this->connection->table('test')->select()->what(['text'])->run() as $result) {
				$result = $result['text'];
			}

			$this->assertEquals('\'; DROP TABLE `test`; -- ', $result);

			$this->truncate();
		}
	}
?>