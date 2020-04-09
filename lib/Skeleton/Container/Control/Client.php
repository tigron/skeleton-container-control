<?php
/**
 * Client class for Skeleton-containers
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 */
namespace Skeleton\Container\Control;

class Client extends \OtherCode\Rest\Rest {

	/**
	 * Set api key
	 *
	 * @access public
	 * @param string $key
	 */
	public function set_key($key) {
		$this->configuration->addHeader('key', $key);
	}

	/**
	 * Set endpoint
	 *
	 * @access public
	 * @param string $endpoint
	 */
	public function set_endpoint($endpoint) {
		$this->configuration->url = $endpoint;
	}

	/**
	 * Get endpoint
	 *
	 * @access public
	 * @return string $endpoint
	 */
	public function get_endpoint() {
		return $this->configuration->url;
	}

	/**
	 * Get
	 *
	 * @access public
	 * @param string $url
	 * @param array $data
	 */
	public function get($url, $data = []) {
		if (count($data) > 0) {
			$query = http_build_query($data);
			$url .= '?' . $query;
		}

		$response = parent::get($url);
		$this->check_error($response);

		return $this->unpack($response->body);
	}

	/**
	 * Post
	 *
	 * @access public
	 * @param string $url
	 * @param array $data
	 */
	public function post($url, $data = []) {
		$response = parent::post($url, json_encode($data));
		$this->check_error($response);

		return $this->unpack($response->body);
	}

	/**
	 * Put
	 *
	 * @access public
	 * @param string $url
	 * @param array $data
	 */
	public function put($url, $data = []) {
		$response = parent::put($url, $data);
		$this->check_error($response);
		return $this->unpack($response->body);
	}

	/**
	 * Patch
	 *
	 * @access public
	 * @param string $url
	 * @param array $data
	 */
	public function patch($url, $data = []) {
		$response = parent::patch($url, $data);
		$this->check_error($response);

		return $this->unpack($response->body);
	}

	/**
	 * delete
	 *
	 * @access public
	 * @param string $url
	 * @param array $data
	 */
	public function delete($url, $data = []) {
		$response = parent::delete($url, $data);
		$this->check_error($response);

		return $this->unpack($response->body);
	}

	/**
	 * Check for errored response
	 *
	 * @access private
	 * @param array $response
	 */
	private function check_error($response) {
		if ($response->code != 200) {
			$body = json_decode($response->body);
			throw new \Exception('Error ' . $response->code . ': ' . $body->message);
		}
	}

	/**
	 * Unpack
	 *
	 * @access private
	 * @param string $response
	 * @return object $payload
	 */
	private function unpack($response) {
		$response = json_decode($response, true);
		if (!isset($response['data'])) {
			return [];
		}
		return $response['data'];
	}

}
