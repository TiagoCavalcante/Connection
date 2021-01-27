<?php
	final class DeleteTest extends Operation {
		public function testCanDoADelete() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run();
			}
			
			$this->connection->table('test')
				->delete()
				->where([['=', 'id',  1]])
				->run();

			$this->assertEquals(
				9,
				$this->connection->table('test')
					->count()
					->what(['*'])
					->run()
			);

			$this->truncate();
		}

		public function testCanDoADeleteLimit() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run();
			}

			if (getenv('name') === 'PgSQL') {
				$this->expectException(Exception::class);
			
				try {
					$this->connection->table('test')
						->delete()
						->where([['=', 'text',  'Hello, world']])
						->limit(5)
						->run();
				}
				finally {
					$this->truncate();
				}
			}
			else {
				$this->connection->table('test')
					->delete()
					->where([['=', 'text',  'Hello, world']])
					->limit(5)
					->run();

				$this->assertEquals(
					5,
					$this->connection->table('test')
						->count()
						->what(['*'])
						->run()
				);

				$this->truncate();
			}
		}
		
		public function testCanDoADeleteComplexWhere() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run();
			}
			
			$this->connection->table('test')
				->delete()
				->where([['=', 'id',  1], 'OR', ['=', 'id', 2]])
				->run();

			$this->assertEquals(
				8,
				$this->connection->table('test')
					->count()
					->what(['*'])
					->run()
			);

			$this->truncate();
		}
	}
?>