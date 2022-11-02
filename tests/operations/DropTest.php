<?php
  final class DropTest extends Operation {
    public function testCanDropATable() : void {
      $this->connection->table('test')
        ->drop()
        ->run();

      $this->expectException(Exception::class);

      $this->connection->table('test')
        ->select()
        ->what('*')
        ->run();
    }
  }
?>