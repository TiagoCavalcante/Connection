<?php
  final class CountTest extends Operation {
    public function testCanDoACount() : void {
      for ($i = 0; $i <= 9; $i++) {
        $this->connection->table('test')
          ->insert()
          ->what('text')
          ->values('Hello, world')
          ->run();
      }

      $this->assertEquals(10, $this->connection->table('test')->count()->what('*')->run());

      $this->truncate();
    }

    public function testCanDoACountWhere() : void {
      for ($i = 0; $i <= 9; $i++) {
        $this->connection->table('test')
          ->insert()
          ->what('text')
          ->values('Hello, world')
          ->run();
      }

      for ($i = 0; $i <= 9; $i++) {
        $this->connection->table('test')
          ->insert()
          ->what('text')
          ->values("Hello, $i")
          ->run();
      }

      $this->assertEquals(
        10,
        $this->connection->table('test')
          ->count()
          ->what('*')
          ->where(['=', 'text', 'Hello, world'])
          ->run()
      );

      $this->truncate();
    }

    public function testCanDoACountComplexWhere() : void {
      for ($i = 0; $i <= 9; $i++) {
        $this->connection->table('test')
          ->insert()
          ->what('text')
          ->values('Hello, world')
          ->run();
      }

      for ($i = 0; $i <= 9; $i++) {
        $this->connection->table('test')
          ->insert()
          ->what('text')
          ->values("Hello, $i")
          ->run();
      }

      $this->assertEquals(
        11,
        $this->connection->table('test')
          ->count()
          ->what('*')
          ->where(['=', 'text', 'Hello, world'], 'OR', ['=', 'id', 19])
          ->run()
      );

      $this->truncate();
    }
  }
?>