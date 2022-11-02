<?php
  final class UpdateTest extends Operation {
    public function testCanDoAUpdate() : void {
      for ($i = 0; $i <= 9; $i++) {
        $this->connection->table('test')
          ->insert()
          ->what('text')
          ->values('Hello, world')
          ->run();
      }
      
      $this->connection->table('test')
        ->update()
        ->what(['=', 'text', 'Hello, you'])
        ->run();

      foreach ($this->connection->table('test')->select()->what('text')->run() as $result) {
        $this->assertEquals('Hello, you', $result['text']);
      }

      $this->connection->table('test')
        ->truncate()
        ->run();
    }

    public function testCanDoAUpdateLimit() : void {
      for ($i = 0; $i <= 9; $i++) {
        $this->connection->table('test')
          ->insert()
          ->what('text')
          ->values('Hello, world')
          ->run();
      }

      if (getenv('name') === 'PgSQL') {
        $this->expectException(Exception::class);

        try {
          $this->connection->table('test')
            ->update()
            ->what(['=', 'text', 'Hello, you'])
            ->limit(5)
            ->run();
        }
        finally {
          $this->connection->table('test')
            ->truncate()
            ->run();
        }
      }
      else {
        $this->connection->table('test')
          ->update()
          ->what(['=', 'text', 'Hello, you'])
          ->limit(5)
          ->run();

        $i = 0;
        foreach ($this->connection->table('test')->select()->what('text')->run() as $result) {
          if ($i < 5) {
            $this->assertEquals('Hello, you', $result['text']);
          }
          else {
            $this->assertEquals('Hello, world', $result['text']);
          }

          $i++;
        }

        $this->connection->table('test')
          ->truncate()
          ->run();
      }
    }

    public function testCanDoAUpdateWhere() : void {
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
      
      $this->connection->table('test')
        ->update()
        ->what(['=', 'text', 'Hello, you'])
        ->where(['=', 'text', 'Hello, world'])
        ->run();

      foreach ($this->connection->table('test')->select()->what('text')->where(['=', 'text', 'Hello, you'])->run() as $result) {
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
      
      $this->connection->table('test')
        ->update()
        ->what(['=', 'text', 'Hello, you'])
        ->where(['=', 'text', 'Hello, world'], 'AND', ['<>', 'id', 1])
        ->run();

      foreach ($this->connection->table('test')->select()->what('text')->where(['=', 'text', 'Hello, you'])->run() as $result) {
        $results[] = $result['text'];
      }

      $this->assertCount(9, $results);

      $this->truncate();
    }
  }
?>