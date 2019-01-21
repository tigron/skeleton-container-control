<?php
/**
 * Container class
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Container;
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
		$client = new Client();
		$client->set_endpoint($this->endpoint);
		$client->set_key($this->key);
		return $client;
	}

	/**
	 * Provision
	 *
	 * @access public
	 * @param Service $service
	 */
	public function provision(Service $service) {
		$client = $this->get_client();
		$client->post('/container?action=provision', [ 'name' => $service->name, 'content' => $service->get_deploy_content() ]);
	}

	/**
	 * Provision
	 *
	 * @access public
	 * @param Service $service
	 */
	public function deprovision(Service $service) {
		$client = $this->get_client();
		$client->post('/container?action=deprovision', [ 'name' => $service->name ]);
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
		$client = new Client();
		$client->set_endpoint($endpoint);
		$key = $client->get('/container?action=pair');

		$client->set_key($key);
		$info = $client->get('/container?action=info');

		$exists = false;
		try {
			$container = self::get_by_name($info['name']);
			$exists = true;
		} catch (\Exception $e) { }

		if ($exists) {
			$client->get('/container?action=unpair');
		} else {
			$container = new self();
			$container->endpoint = $endpoint;
			$container->name = $info['name'];
			$container->key = $key;
			$container->save();
			return $container;
		}
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
			throw new \Exception('Container with name ' . $name . ' not found');
		}
		return self::get_by_id($id);
	}


}
