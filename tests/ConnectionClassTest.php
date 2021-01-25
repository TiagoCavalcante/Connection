<?php
	final class ConnectionClassTest extends PHPUnit\Framework\TestCase {
		private object $connection;

		public function setUp() : void {
			require_once 'env.php';
			require_once 'src/index.php';

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

		public function testCanDoAInsert() : void {
			$this->assertEquals(
				null,
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run()
			);

			$this->connection->table('test')
				->truncate()
				->run();
		}

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

			$this->connection->table('test')
				->truncate()
				->run();
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

			$this->connection->table('test')
				->truncate()
				->run();
		}

		public function testCanDoACount() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run();
			}

			$this->assertEquals(10, $this->connection->table('test')->count()->what(['*'])->run());

			$this->connection->table('test')
				->truncate()
				->run();
		}

		public function testCanDoACountWhere() : void {
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

			$this->assertEquals(
				10,
				$this->connection->table('test')
					->count()
					->what(['*'])
					->where([['=', 'text', 'Hello, world']])
					->run()
			);

			$this->connection->table('test')
				->truncate()
				->run();
		}

		public function testCanDoATruncate() : void {
			for ($i = 0; $i <= 9; $i++) {
				$this->connection->table('test')
					->insert()
					->what(['text'])
					->values(['Hello, world'])
					->run();
			}
			
			$this->connection->table('test')
				->truncate()
				->run();

			$this->assertEquals(
				0,
				$this->connection->table('test')
					->count()
					->what(['*'])
					->run()
			);
		}

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

			$this->connection->table('test')
				->truncate()
				->run();
		}

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

			$this->connection->table('test')
				->truncate()
				->run();
		}
	}
?>