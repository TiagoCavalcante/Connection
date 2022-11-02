<?php
  namespace Connection;

  require_once __DIR__ . '/../Operation.php';

  final class Update extends Operation {
    private array $what;
    private array $where = [];
    private int $limit = 0;

    public function what(array ...$what) : object {
      $this->what = $what;

      return $this;
    }

    public function where(array | string ...$where) : object {
      $this->where = $where;

      return $this;
    }

    public function limit(int $limit) : object {
      $this->limit = $limit;

      return $this;
    }

    private function update() : void {
      $limit = ($this->limit === 0) ? '' : "LIMIT {$this->limit}";

      $what_question_marks = '';
      for ($i = 0; $i < count($this->what); $i++) {
        $what_question_marks .= "{$this->what[$i][1]}{$this->what[$i][0]} ?";
      }

      if ($this->name === 'PgSQL') {
        $query = "UPDATE {$this->table} SET $what_question_marks $limit;";
      }
      else {
        $query = "UPDATE `{$this->table}` SET $what_question_marks $limit;";
      }

      for ($i = 0; $i < count($this->what); $i++) {
        $new_array[] = $this->what[$i][2];
      }

      $statement = $this->connection->prepare($query);
      $statement->execute($new_array);
    }

    private function updateWhere() : void {
      $limit = ($this->limit === 0) ? '' : "LIMIT {$this->limit}";

      $what_question_marks = '';
      for ($i = 0; $i < count($this->what); $i++) {
        $what_question_marks .= "{$this->what[$i][1]}{$this->what[$i][0]} ?";
      }

      $where_question_marks = '';
      for ($i = 0; $i < count($this->where); $i++) {
        if ($i % 2 === 0) {
          $where_question_marks .= "{$this->where[$i][1]}{$this->where[$i][0]} ?";
        }
        else {
          $where_question_marks .= " {$this->where[$i]} ";
        }
      }

      if ($this->name === 'PgSQL') {
        $query = "UPDATE {$this->table} SET $what_question_marks WHERE $where_question_marks $limit;";
      }
      else {
        $query = "UPDATE `{$this->table}` SET $what_question_marks WHERE $where_question_marks $limit;";
      }

      for ($i = 0; $i < count($this->what); $i++) {
        $new_array[] = $this->what[$i][2];
      }
      for ($i = 0; $i < count($this->where); $i += 2) {
        $new_array[] = $this->where[$i][2];
      }

      $statement = $this->connection->prepare($query);
      $statement->execute($new_array);
    }

    public function run() : void {
      if (count($this->where) === 0) {
        $this->update();
      }
      else {
        $this->updateWhere();
      }
    }
  }
?>