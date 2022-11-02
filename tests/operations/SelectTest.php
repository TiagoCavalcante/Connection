<?php
  final class SelectTest extends Operation {
    public function testCanDoASelect() : void {
      for ($i = 0; $i <= 9; $i++) {
        $this->connection->table('test')
          ->insert()
          ->what('text')
          ->values('Hello, world')
          ->run();
      }

      $this->assertCount(10, $this->connection->table('test')->select()->what('text')->run());

      $this->truncate();
    }

    public function testCanDoASelectWhere() : void {
      for ($i = 0; $i <= 9; $i++) {
        $this->connection->table('test')
          ->insert()
          ->what('text')
          ->values("Hello, $i")
          ->run();
      }

      foreach ($this->connection->table('test')->select()->what('text')->where(['=', 'text', 'Hello, 1'])->run() as $result) {
        $result = $result['text'];
      }

      $this->assertEquals('Hello, 1', $result);

      $this->truncate();
    }

    public function testCanDoASelectComplexWhere() : void {
      for ($i = 0; $i <= 9; $i++) {
        $this->connection->table('test')
          ->insert()
          ->what('text')
          ->values("Hello, $i")
          ->run();
      }

      $i = 1;
      foreach ($this->connection->table('test')->select()->what('text')->where(['=', 'text', 'Hello, 1'], 'OR', ['=', 'text', 'Hello, 2'])->run() as $result) {
        $this->assertEquals("Hello, $i", $result['text']);

        $i++;
      }

      $this->truncate();
    }

    public function testCanDoASelectOrderBy() : void {
      $this->connection->table('test')
        ->insert()
        ->what('text')
        ->values('C')
        ->run();
      $this->connection->table('test')
        ->insert()
        ->what('text')
        ->values('A')
        ->run();
      $this->connection->table('test')
        ->insert()
        ->what('text')
        ->values('A')
        ->run();
      $this->connection->table('test')
        ->insert()
        ->what('text')
        ->values('B')
        ->run();

      $rows = $this->connection->table('test')
        ->select()
        ->what('id', 'text')
        ->orderBy(['text'], ['id', 'DESC'])
        ->run();

      $this->assertEquals([
        'id' => 3,
        0 => 3,
        'text' => 'A',
        1 => 'A'
      ], $rows[0]);

      $this->assertEquals([
        'id' => 2,
        0 => 2,
        'text' => 'A',
        1 => 'A'
      ], $rows[1]);

      $this->assertEquals([
        'id' => 4,
        0 => 4,
        'text' => 'B',
        1 => 'B'
      ], $rows[2]);

      $this->assertEquals([
        'id' => 1,
        0 => 1,
        'text' => 'C',
        1 => 'C'
      ], $rows[3]);

      $this->truncate();
    }

    public function testCanDoASelectWhereOrderBy() : void {
      $this->connection->table('test')
        ->insert()
        ->what('text')
        ->values('B')
        ->run();
      $this->connection->table('test')
        ->insert()
        ->what('text')
        ->values('A')
        ->run();
      $this->connection->table('test')
        ->insert()
        ->what('text')
        ->values('A')
        ->run();

      $rows = $this->connection->table('test')
        ->select()
        ->what('id')
        ->where(['=', 'text', 'A'])
        ->orderBy(['id', 'DESC'])
        ->run();

      $this->assertEquals([
        'id' => 3,
        0 => 3,
      ], $rows[0]);

      $this->assertEquals([
        'id' => 2,
        0 => 2,
      ], $rows[1]);

      $this->truncate();
    }
  }
?>