<?php
  namespace Connection;

  require_once __DIR__ . '/../Operation.php';

  final class Truncate extends Operation {
    public function run() : void {
      if ($this->name === 'PgSQL') {
        $this->connection->exec("TRUNCATE TABLE {$this->table};");
      }
      elseif ($this->name === 'MySQL') {
        $this->connection->exec("TRUNCATE TABLE `{$this->table}`;");
      }
      else {
        $this->connection->exec("DELETE FROM `{$this->table}`;");
      }
    }
  }
?>