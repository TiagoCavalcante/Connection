<?php
	final class AutoloadFileTest extends PHPUnit\Framework\TestCase {
		public function setUp() : void {
			require_once __DIR__ . '/../vendor/autoload.php';
		}

		public function testMigrationsClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Migrations'));
		}

		public function testConnecionClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Connection'));
		}
	}
?>