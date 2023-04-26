<?php
/**
 * Database migration class
 *
 */

namespace Skeleton\Container\Control;

use \Skeleton\Database\Database;

class Migration_20230426_170000_created extends \Skeleton\Database\Migration {

	/**
	 * Migrate up
	 *
	 * @access public
	 */
	public function up() {
		$db = Database::get();
		$db->query("
			ALTER TABLE `container`
			ADD `created` text COLLATE 'utf8_unicode_ci' NULL;		
		");
	}

	/**
	 * Migrate down
	 *
	 * @access public
	 */
	public function down() {}
}
