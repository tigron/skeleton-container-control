<?php
/**
 * Package class
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Container;

class Service {

	/**
	 * Path
	 *
	 * @var string $path
	 * @access public
	 */
	public $path = null;

	/**
	 * Lib Path
	 *
	 * @var string $lib_path
	 * @access public
	 */
	public $lib_path = null;

	/**
	 * Module Path
	 *
	 * @var string $module_path
	 * @access public
	 */
	public $module_path = null;

	/**
	 * Name
	 *
	 * @var string $name
	 * @access public
	 */
	public $name = null;

	/**
	 * Get deloy content
	 *
	 * @access public
	 * @return array $content
	 */
	public function get_deploy_content() {
		$files = $this->glob_recursive($this->path . '/*');
		foreach ($files as $key => $file) {
			$files[$key] = str_replace($this->path, '', $file);
		}

		$zip = new \ZipArchive();

		$zip->open(\Skeleton\Core\Config::$tmp_dir .'/package.zip', \ZipArchive::CREATE);

		foreach ($files as $file) {
			if (is_dir($this->path . '/' . $file)) {
				$zip->addEmptyDir($file);
			} else {
				$zip->addFile($this->path . '/' . $file, $file);
			}
		}
		$zip->close();

		$content = base64_encode(file_get_contents(\Skeleton\Core\Config::$tmp_dir .'/package.zip'));
		unlink(\Skeleton\Core\Config::$tmp_dir .'/package.zip');
		return $content;
	}

	/**
	 * Recursive glob
	 *
	 * @access private
	 * @param string $pattern
	 * @param string $flags
	 */
	private function glob_recursive($pattern, $flags = 0) {
		$files = glob($pattern, $flags);

		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
			$files = array_merge($files, $this->glob_recursive($dir.'/'.basename($pattern), $flags));
		}

		return $files;
	}

	/**
	 * Get all
	 *
	 * @access public
	 * @return array $applications
	 */
	public static function get_all() {
		if (Config::$service_dir === null) {
			throw new \Exception('No service_dir set. Please set Config::$service_dir');
		}
		$service_directories = scandir(Config::$service_dir);
		$services = [];
		foreach ($service_directories as $service_directory) {
			if ($service_directory[0] == '.') {
				continue;
			}

			$service_path = realpath(Config::$service_dir . '/' . $service_directory);
			$service = new Service();
			$service->module_path = $service_path . '/module/';
			$service->lib_path = $service_path . '/lib/';
			$service->path = $service_path;
			$service->name = $service_directory;
			$services[] = $service;
		}
		return $services;
	}

	/**
	 * Get by name
	 *
	 * @access public
	 * @param string $name
	 * @return Service $service
	 */
	public static function get_by_name($name) {
		$services = self::get_all();
		foreach ($services as $service) {
			if ($service->name == $name) {
				return $service;
			}
		}
		throw new \Exception('Service with name ' . $name . ' not found');
	}

}
