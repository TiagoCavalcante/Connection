<?php
	namespace Connection;

	final class SQL {
		# consts
		const SELECT = 'select';
		const COUNT = 'count';
		const INSERT = 'insert';
		const CREATE = 'create';
		const DROP = 'drop';
		const TRUNCATE = 'truncate';
		const DELETE = 'delete';
	}

	abstract class Connection {
		# vars
		private object $connection;

		# closer
		abstract public function close() : void;

		# functions
		# generate function (to generate the SQL code)
		abstract public function generate(SQL $type) : string;
		# query functions
		abstract public function select(string $from, string $what = '*', string $where = null) : object;
		abstract public function count(string $from, string $what = '*', string $where = null) : object;
		abstract public function insert(string $table, string $what, string $values) : void;
		abstract public function create(string $table, string $columns) : void;
		abstract public function drop(string $table) : void;
		abstract public function truncate(string $table) : void;
		# SQL functions
		abstract public function affectedRows() : int;
		# prevent function (to prevent SQL injection)
		abstract public function prevent(string $value) : string;
	}
?>