<?php
	final class AutoloadFileTest extends PHPUnit\Framework\TestCase {
		public function setUp() : void {
			require_once __DIR__ . '/../vendor/autoload.php';
		}

		public function testMySQLClassExist() : void {
			$this->assertTrue(\class_exists('Connection\MySQL'));
		}

		public function testPgSQLClassExist() : void {
			$this->assertTrue(\class_exists('Connection\PgSQL'));
		}

		public function testSQLiteClassExist() : void {
			$this->assertTrue(\class_exists('Connection\SQLite'));
		}
	}
?>