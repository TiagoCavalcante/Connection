<?php
	final class AutoloadTest extends PHPUnit\Framework\TestCase {
		public function setUp() : void {
			require_once __DIR__ . '/../vendor/autoload.php';
		}

		public function testConnecionClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Connection'));
		}

		public function testOperationClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Operation'));
		}

		public function testQueryClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Query'));
		}

		public function testCountClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Count'));
		}

		public function testCreateClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Create'));
		}

		public function testDeleteClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Delete'));
		}

		public function testDropClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Drop'));
		}

		public function testInsertClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Insert'));
		}

		public function testSelectClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Select'));
		}

		public function testTruncateClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Truncate'));
		}

		public function testUpdateClassExist() : void {
			$this->assertTrue(\class_exists('Connection\Update'));
		}
	}
?>