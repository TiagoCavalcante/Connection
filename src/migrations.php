<?php
	namespace Connection;

	final class Migrations {
		private static function requireFile(string $dir, string $type) {
			require_once $dir;

			if ($type == 'run')
				$run();
			if ($type == 'back')
				$back();

			# close the connection (necessary for security)
			$connection->close();
		}

		public static function new(string $migration) {
			$file = fopen($migration, 'w');
			fwrite($file, "<?php\n\trequire __DIR__ . '/../vendor/autoload.php';\n\trequire __DIR__ . '/../.env.php';\n\n\t\$connection = new Connection\SQLite();\n\n\t\$run = function() use (\$connection) {\n\t\t\$connection->create('posts', [\n\t\t\t'text' => 'TEXT'\n\t\t]);\n\t};\n\n\t\$back = function() use (\$connection) {\n\t\t\$connection->drop('posts');\n\t};\n?>");
			fclose($file);

			if (!file_exists('env.php')) {
				$env = fopen('env.php', 'w');
				fwrite($env, "<?php\n\tputenv('database=databases/database.sqlite3');\n?>");
				fclose($env);
			}
		}

		public static function run(string $migration) {
			self::requireFile($migration, 'run');
		}

		public static function back(string $migration) {
			self::requireFile($migration, 'back');
		}
	}
?>