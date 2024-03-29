<?php
  namespace Connection;

  require_once __DIR__ . '/../Operation.php';

  final class Create extends Operation {
    private array $columns;

    public function columns(array $columns) : object {
      $this->columns = $columns;

      return $this;
    }

    public function run() : void {
      if ($this->name === 'PgSQL') {
        $query = "CREATE TABLE IF NOT EXISTS {$this->table} (";
      }
      else {
        $query = "CREATE TABLE IF NOT EXISTS `{$this->table}` (";
      }

      $i = 0;
      foreach ($this->columns as $columm => $value) {
        if (str_contains($value, 'PRIMARY')) {
          if ($this->name === 'MySQL' || $this->name === 'MariaDB') {
            $value = str_replace(' PRIMARY', ' AUTO_INCREMENT PRIMARY KEY', $value);
          }
          elseif ($this->name === 'PgSQL') {
            $value = str_replace(' PRIMARY', ' PRIMARY KEY GENERATED ALWAYS AS IDENTITY', $value);
          }
          else {
            $value = str_replace('INT PRIMARY', 'INTEGER PRIMARY KEY AUTOINCREMENT', $value);
          }
        }

        if ($this->name === 'PgSQL') {
          $query .= "$columm $value";
        }
        else {
          $query .= "`$columm` $value";
        }

        # if it isn't the last element of the array
        if ($i < count($this->columns) - 1) {
          $query .= ',';
        }

        $i++;
      }

      if ($this->name === 'MySQL') {
        $query .= ') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;';
      }
      else if ($this->name === 'MariaDB') {
        $query .= ') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;';
      }
      else {
        $query .= ');';
      }

      $this->connection->exec($query);
    }
  }
?>