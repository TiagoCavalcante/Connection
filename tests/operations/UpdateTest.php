<?php
	final class UpdateTest extends Operation {
		public function testCanDoAUpdate() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run();
			}
			
			$this->connection->table('test')
				->update()
				->what([['=', 'text', 'Hello, you']])
				->run();

			foreach ($this->connection->table('test')->select()->what(['text'])->run() as $result) {
				$this->assertEquals('Hello, you', $result['text']);
			}

			$this->connection->table('test')
				->truncate()
				->run();
		}

		public function testCanDoAUpdateWhere() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run();
			}

			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(["Hello, $i"])
					->run();
			}
			
			$this->connection->table('test')
				->update()
				->what([['=', 'text', 'Hello, you']])
				->where([['=', 'text', 'Hello, world']])
				->run();

			foreach ($this->connection->table('test')->select()->what(['text'])->where([['=', 'text', 'Hello, you']])->run() as $result) {
				$results[] = $result['text'];
			}

			$this->assertCount(10, $results);

			$this->connection->table('test')
				->truncate()
				->run();
		}

		public function testCanDoAUpdateComplexWhere() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run();
			}

			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(["Hello, $i"])
					->run();
			}
			
			$this->connection->table('test')
				->update()
				->what([['=', 'text', 'Hello, you']])
				->where([['=', 'text', 'Hello, world'], 'AND', ['<>', 'id', 1]])
				->run();

			foreach ($this->connection->table('test')->select()->what(['text'])->where([['=', 'text', 'Hello, you']])->run() as $result) {
				$results[] = $result['text'];
			}

			$this->assertCount(9, $results);

			$this->truncate();
		}
	}
?>