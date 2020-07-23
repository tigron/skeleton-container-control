<?php
/**
 * Container class
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Container\Control;
use \Skeleton\Database\Database;

class Container {
	use \Skeleton\Object\Get;
	use \Skeleton\Object\Model;
	use \Skeleton\Object\Delete;
	use \Skeleton\Object\Save;

	/**
	 * Get client
	 *
	 * @access public
	 * @return Client $client
	 */
	public function get_client() {
		$client = new Client($this->endpoint, $this->ssl_hostname, $this->ssl_certificate);
		$client->set_key($this->key);
		return $client;
	}

	/**
	 * Get container_service
	 *
	 * @access public
	 * @param \Skeleton\Container\Control\Service $service
	 * @return $container_service
	 */
	public function get_container_service(Service $service) {
		return \Skeleton\Container\Control\Container\Service::get_by_container_service($this, $service);
	}

	/**
	 * Provision
	 *
	 * @access public
	 * @param Service $service
	 */
	public function provision(Service $service) {
		$client = $this->get_client();
		$client->post('/container?action=provision', [ $service->name, $service->get_deploy_content() ]);
	}

	/**
	 * Provision
	 *
	 * @access public
	 * @param Service $service
	 */
	public function deprovision(Service $service) {
		$client = $this->get_client();
		$client->post('/container?action=deprovision', [ $service->name ]);
	}

	/**
	 * Get info
	 *
	 * @access public
	 */
	public function info() {
		$client = $this->get_client();
		$info = $client->get('/container?action=info');
		return $info;
	}

	/**
	 * Unpair
	 *
	 * @access public
	 */
	public function unpair() {
		$client = $this->get_client();
		$info = $client->get('/container?action=unpair');
		$this->delete();
	}

	/**
	 * Pair
	 *
	 * @access public
	 * @param string $name
	 * @param string $endpoint
	 * @return Container $container
	 */
	public static function pair($endpoint) {
		$client = new Client($endpoint, null, null, false);
		$key = $client->get('/container?action=pair');

		$client->set_key($key);
		$info = $client->get('/container?action=info');

		$exists = false;
		try {
			$container = self::get_by_name($info['name']);
			$exists = true;
		} catch (Exception\Container $e) { }

		if (!$exists) {
			$container = new self();
			$container->endpoint = $endpoint;
			$container->name = $info['name'];
			$container->key = $key;

			if (Util::is_self_signed($endpoint)) {
				$container->load_array(Util::get_self_signed_info($endpoint));
			}

			$container->save();
		}

		return $container;
	}

	/**
	 * Get by name
	 *
	 * @access public
	 * @param string $name
	 * @return Container $container
	 */
	public static function get_by_name($name) {
		$db = Database::get();
		$id = $db->get_one('SELECT id FROM container WHERE name=?', [ $name ]);

		if ($id === null) {
			throw new Exception\Container('Container with name ' . $name . ' not found');
		}

		return self::get_by_id($id);
	}
}
