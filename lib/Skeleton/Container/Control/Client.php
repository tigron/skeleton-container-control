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
	/**
	 * @var string $endpoint
	 * @access private
	 */
	private $endpoint = null;

	/**
	 * @var string $key
	 * @access private
	 */
	private $key = null;

	/**
	 * @var GuzzleHttp\Client $client
	 * @access private
	 */
	private $client;

	/**
	 * @var string $ca_file
	 * @access private
	 */
	private $ca_file = null;

	/**
	 * Constructor
	 *
	 * @param string $endoint
	 */
	public function __construct(string $endpoint, string $ssl_hostname = null, string $ssl_certificate = null, bool $ssl_verify = true) {
		$scheme = parse_url($endpoint, PHP_URL_SCHEME);
		$host = parse_url($endpoint, PHP_URL_HOST);
		$port = parse_url($endpoint, PHP_URL_PORT);

		if ($port === null && $scheme === 'http') {
			$port = 80;
		} elseif ($port === null && $scheme === 'https') {
			$port = 443;
		}

		// If we are going to use SSL, make sure verification works if we're using a self-signed certificate
		if ($scheme === 'https' && $ssl_hostname !== null && $ssl_certificate !== null && $ssl_hostname != $host) {
			$connect_host = $ssl_hostname;

			$this->ca_file = tempnam(sys_get_temp_dir(), 'ca_' . $connect_host . '-');
			file_put_contents($this->ca_file, $ssl_certificate);

			$verify = $this->ca_file;
			$ip = gethostbyname($host);

			$curl_options = [
				CURLOPT_RESOLVE => [$connect_host . ':' . $port . ':' . $ip],
			];
		} else {
			$connect_host = $host;
			$verify = true;
			$curl_options = [];
		}

		// If we override verification, apply it
		if ($ssl_verify !== true) {
			$verify = $ssl_verify;
		}

		$this->endpoint = $endpoint;

		$this->client = new \GuzzleHttp\Client([
			'base_uri' => $scheme . '://' . $connect_host . ':' . $port,
			'http_errors' => false,
			'verify' => $verify,
			'curl' => $curl_options,
		]);
	}

	/**
	 * Destructor, clean up temporary files
	 *
	 * @param string $endoint
	 */
	function __destruct() {
		if ($this->ca_file !== null && is_file($this->ca_file)) {
			unlink($this->ca_file);
		}
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
