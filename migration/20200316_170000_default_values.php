<?php
/**
 * Database migration class
 *
 */
namespace Skeleton\Container\Control;


use \Skeleton\Database\Database;

class Migration_20200316_170000_Default_Values extends \Skeleton\Database\Migration {

	/**
	 * Migrate up
	 *
	 * @access public
	 */
	public function up() {
return; // temporarliy disabled by LL (20200331) to be able to continue to work
		$db = Database::get();
		$db->query("
		", []);

		$db->query("
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
