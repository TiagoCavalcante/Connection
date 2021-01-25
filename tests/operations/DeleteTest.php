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