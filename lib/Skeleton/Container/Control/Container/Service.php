<?php
/**
 * Container\Service class
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Container\Control\Container;

class Service {

	/**
	 * Container
	 *
	 * @access public
	 * @var \Skeleton\Container\Control\Container
	 */
	public $container = null;

	/**
	 * Service
	 *
	 * @access public
	 * @var \Skeleton\Container\Control\Service
	 */
	public $service = null;

	/**
	 * Environment
	 *
	 * @access public
	 * @var string $environment
	 */
	private $environment = 'index';

	/**
	 * Constructor
	 *
	 * @access private
	 * @param \Skeleton\Container\Control\Container $container
	 * @param \Skeleton\Container\Control\Service $service
	 */
	private function __construct(\Skeleton\Container\Control\Container $container, \Skeleton\Container\Control\Service $service) {
		$this->container = $container;
		$this->service = $service;
	}

	/**
	 * Call a function
	 *
	 * @access public
	 */
	public function __call($method, $arguments) {
		$client = $this->container->get_client();
		return $client->post( '/' . $this->service->name . '/' . $this->environment . '?action=' . $method, $arguments);
	}

	/**
	 * Set environment
	 *
	 * @access public
	 * @param string $environment
	 */
	public function set_environment($environment = 'production') {
		if ($environment == 'production') {
			$this->environment = 'index';
		} else {
			$this->environment = 'debug';
		}
	}

	/**
	 * Get by container and service
	 *
	 * @access public
	 * @param \Skeleton\Container\Control\Container $container
	 * @param \Skeleton\Container\Control\Service $service
	 * @return Container $container
	 */
	public static function get_by_container_service(\Skeleton\Container\Control\Container $container, \Skeleton\Container\Control\Service $service) {
		$info = $container->info();
		$deployed_services = $info['services'];

		foreach ($deployed_services as $deployed_service) {
			if ($deployed_service == $service->name) {
				$container_service = new self($container, $service);
				return $container_service;
			}
		}

		throw new \Skeleton\Container\Control\Exception\Service('Service ' . $service->name . ' does not exist for container ' . $container->name);
	}
}
