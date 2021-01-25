<?php
	final class SelectTest extends Operation {
		public function testCanDoASelect() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run();
			}

			foreach ($this->connection->table('test')->select()->what(['text'])->run() as $result) {
				$results[] = $result[0];
			}

			$this->assertCount(10, $results);

			$this->truncate();
		}

		public function testCanDoASelectWhere() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(["Hello, $i"])
					->run();
			}

			foreach ($this->connection->table('test')->select()->what(['text'])->where([['=', 'text', 'Hello, 1']])->run() as $result) {
				$result = $result['text'];
			}

			$this->assertEquals('Hello, 1', $result);

			$this->truncate();
		}

		public function testCanDoASelectComplexWhere() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(["Hello, $i"])
					->run();
			}

			$i = 1;
			foreach ($this->connection->table('test')->select()->what(['text'])->where([['=', 'text', 'Hello, 1'], 'OR', ['=', 'text', 'Hello, 2']])->run() as $result) {
				$this->assertEquals("Hello, $i", $result['text']);

				$i++;
			}

			$this->truncate();
		}
	}
?>