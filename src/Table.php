<?php
  namespace Connection;

  require_once __DIR__ . '/Operation.php';

  require_once __DIR__ . '/operations/Select.php';
  require_once __DIR__ . '/operations/Count.php';
  require_once __DIR__ . '/operations/Insert.php';
  require_once __DIR__ . '/operations/Update.php';
  require_once __DIR__ . '/operations/Create.php';
  require_once __DIR__ . '/operations/Drop.php';
  require_once __DIR__ . '/operations/Truncate.php';
  require_once __DIR__ . '/operations/Delete.php';

  final class Table extends Operation {
    public function select() : object {
      return new Select($this->connection, $this->name, $this->table);
    }

    public function count() : object {
      return new Count($this->connection, $this->name, $this->table);
    }

    public function insert() : object {
      return new Insert($this->connection, $this->name, $this->table);
    }

    public function update() : object {
      return new Update($this->connection, $this->name, $this->table);
    }

    public function create() : object {
      return new Create($this->connection, $this->name, $this->table);
    }

    public function drop() : object {
      return new Drop($this->connection, $this->name, $this->table);
    }

    public function truncate() : object {
      return new Truncate($this->connection, $this->name, $this->table);
    }

    public function delete() : object {
      return new Delete($this->connection, $this->name, $this->table);
    }
  }
?>