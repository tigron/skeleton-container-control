<?php
/**
 * Client class for Skeleton-containers
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 * @author David Vandemaele <david@tigron.be>
 */
namespace Skeleton\Container\Control;

class Client {
	private $endpoint = null;
	private $key = null;
	private $client;

	/**
	 * Constructor
	 *
	 * @param string $endoint
	 */
	public function __construct(string $endpoint) {
		$this->endpoint = $endpoint;
		$this->client = new \GuzzleHttp\Client([
			'base_uri' => $endpoint,
			'http_errors' => false,
		]);
	}

	/**
	 * Set api key
	 *
	 * @access public
	 * @param string $key
	 */
	public function set_key(string $key) {
		$this->key = $key;
	}

	/**
	 * Get endpoint
	 *
	 * @access public
	 * @return string $endpoint
	 */
	public function get_endpoint() {
		return $this->endpoint;
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

		$response = $this->client->request('GET', $url, [
			'headers' => $this->get_headers(),
		]);

		$this->check_error($response);

		return $this->unpack($response->getBody());
	}

	/**
	 * Post
	 *
	 * @access public
	 * @param string $url
	 * @param array $data
	 */
	public function post($url, $data = []) {
		$response = $this->client->request('POST', $url, [
			'headers' => $this->get_headers(),
		    'body' => json_encode($data),
		]);

		$this->check_error($response);

		return $this->unpack($response->getBody());
	}

	/**
	 * Check for errored response
	 *
	 * @access private
	 * @param array $response
	 */
	private function check_error($response) {
		if ($response->getStatusCode() != 200) {
			$body = json_decode($response->getBody());
			throw new Exception\Server('Error ' . $response->getStatusCode() . ': ' . $body->message);
		} else {
			$body = json_decode($response->getBody());

			if ($body === null and !empty((string)$response->getBody())) {
				throw new Exception\Response('Response could not be decoded: ' . $response->getBody());
			}
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

	/**
	 * Get headers for use by the HTTP client
	 *
	 * @access private
	 * @return array $headers
	 */
	private function get_headers() : array {
		if ($this->key !== null) {
			return ['key' => $this->key];
		} else {
			return [];
		}
	}
}
