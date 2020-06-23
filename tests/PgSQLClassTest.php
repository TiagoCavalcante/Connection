<?php
	final class PgSQLClassTest extends PHPUnit\Framework\TestCase {
		private object $connection;

		public function setUp() : void {
			require_once 'PgSQL.env.php';
			require_once 'src/databases/PgSQL.php';

			$this->connection = new Connection\PgSQL();
			$this->connection->create('test', [
				'id' => 'INT PRIMARY KEY GENERATED ALWAYS AS IDENTITY',
				'text' => 'TEXT'
			]);
		}

		public function tearDown() : void {
			$this->connection->drop('test');
			$this->connection->close();
		}

		public function testCanDoAInsert() : void {
			$this->assertEquals(null, $this->connection->insert('test', 'text', "'Hello, world'"));

			$this->connection->truncate('test');
		}

		public function testCanDoASelect() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, world'");
			$results = [];
			foreach ($this->connection->select('test', 'text') as $res)
				$results[] = $res['text'];

			$this->assertCount(10, $results);

			$this->connection->truncate('test');
		}

		public function testCanDoASelectWhere() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, $i'");

			foreach ($this->connection->select('test', 'text', "text = 'Hello, 1'") as $res)
				$result = $res['text'];

			$this->assertEquals('Hello, 1', $result);

			$this->connection->truncate('test');
		}

		public function testCanDoACount() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, world'");
			
			foreach ($this->connection->count('test') as $res)
				$result = $res['count'];

			$this->assertEquals(10, $result);

			$this->connection->truncate('test');
		}

		public function testCanDoACountWhere() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, world'");

			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, $i'");
			
			foreach ($this->connection->count('test', '*', "text = 'Hello, world'") as $res)
				$result = $res['count'];

			$this->assertEquals(10, $result);

			$this->connection->truncate('test');
		}

		public function testCanDoATruncate() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, world'");
			
			$this->connection->truncate('test');

			foreach ($this->connection->count('test', '*') as $res)
				$result = $res['count'];

			$this->assertEquals(0, $result);

			$this->connection->truncate('test');
		}

		public function testCanDoADelete() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, world'");
			
			$result = $this->connection->delete('test', "id = 1");

			foreach ($this->connection->count('test', '*') as $res)
				$result = $res['count'];

			$this->assertEquals(9, $result);

			$this->connection->truncate('test');
		}

		public function testCanDoAUpdate() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, world'");
			
			$result = $this->connection->update('test', "text = 'Hello, you'");

			foreach ($this->connection->select('test', 'text') as $res)
				$this->assertEquals('Hello, you', $res['text']);

			$this->connection->truncate('test');
		}

		public function testCanDoAUpdateWhere() : void {
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, world'");

			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, $i'");
			
			$result = $this->connection->update('test', "text = 'Hello, you'", "text = 'Hello, world'");

			foreach ($this->connection->select('test', 'text', "text = 'Hello, you'") as $res)
				$results[] = $res['text'];

			$this->assertCount(10, $results);

			$this->connection->truncate('test');
		}

		public function testCanDoAInsertWithoutSQLInjection() : void {
			$prepare = $this->connection->prepare(Connection\QueryTypes::INSERT, 'test', 'text', ':text');
			$prepare->bindValue(':text', 'I\'m fine');
			$prepare->execute();

			foreach ($this->connection->select('test', 'text') as $res)
				$result = $res['text'];

			$this->assertEquals('I\'m fine', $result);

			$this->connection->truncate('test');
		}

		public function testAffectedRowsReturnTheCorrectNumber() : void {
			$this->connection->insert('test', 'text', "'Hello'");
			
			for ($i = 0; $i <= 9; $i++)
				$this->connection->insert('test', 'text', "'Hello, world'");

			$prepare = $this->connection->prepare(Connection\QueryTypes::DELETE, 'test', "text = 'Hello, world'");
			$prepare->execute();

			$this->assertEquals(10, $this->connection->affectedRows($prepare));

			$this->connection->truncate('test');
		}
	}
?>