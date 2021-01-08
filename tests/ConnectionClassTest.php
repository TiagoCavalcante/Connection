<?php
	final class ConnectionClassTest extends PHPUnit\Framework\TestCase {
		private object $connection;

		public function setUp() : void {
			require_once 'env.php';
			require_once 'src/index.php';

			$this->connection = new Connection\Connection();
			$this->connection->create('test', [
				'id' => 'INT PRIMARY', 
				'text' => 'TEXT'
			]);
		}

		public function tearDown() : void {
			$this->connection->drop('test');
			$this->connection->close();
		}

		public function testCanDoAInsert() : void {
			$this->assertEquals(null, $this->connection->insert('test', ['text'], ['Hello, world']));

			$this->connection->truncate('test');
		}

		public function testCanDoASelect() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', ['text'], ['Hello, world']);
			$results = [];
			foreach ($this->connection->select('test', ['text']) as $result)
				$results[] = $result[0];

			$this->assertCount(10, $results);

			$this->connection->truncate('test');
		}

		public function testCanDoASelectWhere() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', ['text'], ["Hello, $i"]);
			foreach ($this->connection->select('test', ['text'], [['=', 'text', 'Hello, 1']]) as $res)
				$result = $res['text'];

			$this->assertEquals('Hello, 1', $result);

			$this->connection->truncate('test');
		}

		public function testCanDoACount() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', ['text'], ['Hello, world']);

			$this->assertEquals(10, $this->connection->count('test'));

			$this->connection->truncate('test');
		}

		public function testCanDoACountWhere() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', ['text'], ['Hello, world']);

			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', ['text'], ["Hello, $i"]);

			$this->assertEquals(10, $this->connection->count('test', ['*'], [['=', 'text', 'Hello, world']]));

			$this->connection->truncate('test');
		}

		public function testCanDoATruncate() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', ['text'], ['Hello, world']);
			
			$this->connection->truncate('test');

			$this->assertEquals(0, $this->connection->count('test'));

			$this->connection->truncate('test');
		}

		public function testCanDoADelete() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', ['text'], ['Hello, world']);
			
			$this->connection->delete('test', [['=', 'id',  1]]);

			$this->assertEquals(9, $this->connection->count('test'));

			$this->connection->truncate('test');
		}

		public function testCanDoAUpdate() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', ['text'], ['Hello, world']);
			
			$this->connection->update('test', [['=', 'text', 'Hello, you']]);

			foreach ($this->connection->select('test', ['text']) as $res)
				$this->assertEquals('Hello, you', $res['text']);

			$this->connection->truncate('test');
		}

		public function testCanDoAUpdateWhere() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', ['text'], ['Hello, world']);

			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', ['text'], ["Hello, $i"]);
			
			$this->connection->update('test', [['=', 'text', 'Hello, you']], [['=', 'text', 'Hello, world']]);

			foreach ($this->connection->select('test', ['text'], [['=', 'text', 'Hello, you']]) as $res)
				$results[] = $res['text'];

			$this->assertCount(10, $results);

			$this->connection->truncate('test');
		}

		public function testCanDoAInsertWithoutSQLInjection() : void {
			$this->connection->insert('test', ['text'], ['\'; DROP TABLE `test`; -- ']);

			foreach ($this->connection->select('test', ['text']) as $res)
				$result = $res['text'];

			$this->assertEquals('\'; DROP TABLE `test`; -- ', $result);

			$this->connection->truncate('test');
		}
	}
?>