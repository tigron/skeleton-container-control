<?php
/**
 * Database migration class
 *
 */
namespace Skeleton\Container;


use \Skeleton\Database\Database;

class Migration_20190116_152918_Init extends \Skeleton\Database\Migration {

	/**
	 * Migrate up
	 *
	 * @access public
	 */
	public function up() {
		$db = Database::get();
		$db->query("
			CREATE TABLE `container` (
			  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `name` varchar(128) NOT NULL,
			  `endpoint` varchar(128) NOT NULL
			);
		", []);

		$db->query("
			ALTER TABLE `container`
			ADD `key` varchar(255) COLLATE 'utf8_unicode_ci' NOT NULL;
		", []);
	}

	/**
	 * Migrate down
	 *
	 * @access public
	 */
	public function down() {

	}
}
