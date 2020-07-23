<?php
/**
 * Database migration class
 *
 */

namespace Skeleton\Container\Control;

use \Skeleton\Database\Database;

class Migration_20200723_114612_Ssl extends \Skeleton\Database\Migration {

	/**
	 * Migrate up
	 *
	 * @access public
	 */
	public function up() {
		$db = Database::get();
		$db->query("ALTER TABLE `container` ADD `ssl_hostname` varchar(128) COLLATE 'utf8_unicode_ci' NULL, ADD `ssl_certificate` text COLLATE 'utf8_unicode_ci' NULL AFTER `ssl_hostname`");
	}

	/**
	 * Migrate down
	 *
	 * @access public
	 */
	public function down() {}
}
