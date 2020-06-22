<?php
	final class SQLiteClassTest extends PHPUnit\Framework\TestCase {
		private object $connection;

		public function setUp() : void {
			require_once 'SQLite.env.php';
			require_once 'src/databases/SQLite.php';

			$this->connection = new Connection\SQLite();
			$this->connection->create('test', [
				'text' => 'TEXT'
			]);
		}

		public function tearDown() : void {
			$this->connection->drop('test');
			$this->connection->close();
		}

		public function testCanDoAInsert() : void {
			$this->connection->insert('test', '`text`', "'Hello, world'");

			$this->assertEquals(1, $this->connection->affectedRows());

			$this->connection->truncate('test');
		}

		public function testCanDoASelect() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, world'");
			$result = $this->connection->select('test', '`text`');
			$results = [];
			while ($res = $this->connection->nextResult($result))
				$results[] = $res['text'];

			$this->assertCount(10, $results);

			$this->connection->truncate('test');
		}

		public function testCanDoASelectWhere() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, $i'");
			$result = $this->connection->select('test', '`text`');

			$result = $this->connection->select('test', '`text`', "`text` LIKE 'Hello, 1'");
			$result = $this->connection->nextResult($result);
			$result = $result['text'];

			$this->assertEquals('Hello, 1', $result);

			$this->connection->truncate('test');
		}

		public function testCanDoACount() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, world'");
			
			$result = $this->connection->count('test');
			$result = $this->connection->nextResult($result);
			$result = $result['COUNT(*)'];

			$this->assertEquals(10, $result);

			$this->connection->truncate('test');
		}

		public function testCanDoACountWhere() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, world'");

			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, $i'");
			
			$result = $this->connection->count('test', '*', "`text` = 'Hello, world'");
			$result = $this->connection->nextResult($result);
			$result = $result['COUNT(*)'];

			$this->assertEquals(10, $result);

			$this->connection->truncate('test');
		}

		public function testCanDoATruncate() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, world'");
			
			$this->connection->truncate('test');
			$result = $this->connection->count('test');
			$result = $this->connection->nextResult($result);
			$result = $result['COUNT(*)'];

			$this->assertEquals(0, $result);

			$this->connection->truncate('test');
		}

		public function testCanDoADelete() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, world'");
			
			$result = $this->connection->delete('test', "`ROWID` = 1");

			$result = $this->connection->count('test');
			$result = $this->connection->nextResult($result);
			$result = $result['COUNT(*)'];

			$this->assertEquals(9, $result);

			$this->connection->truncate('test');
		}

		public function testCanDoAUpdate() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, world'");
			
			$result = $this->connection->update('test', "`text` = 'Hello, you'");

			$result = $this->connection->select('test', '`text`');
			while ($res = $this->connection->nextResult($result))
				$this->assertEquals('Hello, you', $res['text']);

			$this->connection->truncate('test');
		}

		public function testCanDoAUpdateWhere() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, world'");

			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, $i'");
			
			$result = $this->connection->update('test', "`text` = 'Hello, you'", "`text` = 'Hello, world'");

			$result = $this->connection->select('test', '`text`', "`text` = 'Hello, you'");
			$results = [];
			while ($res = $this->connection->nextResult($result))
				$results[] = $res['text'];

			$this->assertCount(10, $results);

			$this->connection->truncate('test');
		}

		public function testCanDoAInsertWithoutSQLInjection() : void {
			$prepare = $this->connection->prepare(Connection\QueryTypes::INSERT, 'test', '`text`', ':text');
			$prepare->bindValue(':text', 'I\'m fine');
			$prepare->execute();

			$result = $this->connection->select('test', '`text`');
			$result = $this->connection->nextResult($result);
			$result = $result['text'];

			$this->assertEquals('I\'m fine', $result);

			$this->connection->truncate('test');
		}

		public function testAffectedRowsReturnTheCorrectNumber() : void {
			$this->connection->insert('test', '`text`', "'Hello'");
			
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', '`text`', "'Hello, world'");

			$result = $this->connection->delete('test', "`text` = 'Hello, world'");

			$this->assertEquals(10, $this->connection->affectedRows());

			$this->connection->truncate('test');
		}
	}
?>