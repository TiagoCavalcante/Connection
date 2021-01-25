<?php
	final class TruncateTest extends Operation {
		public function testCanTruncateATable() {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run();
			}

			$this->truncate();

			$this->assertEquals(0, $this->connection->table('test')->count()->what(['*'])->run());
		}
	}
?>