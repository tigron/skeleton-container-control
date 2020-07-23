<?php
/**
 * General utilities
 *
 * @author Christophe Gosiau <christophe@tigron.be>
 * @author Gerry Demaret <gerry@tigron.be>
 */

namespace Skeleton\Container\Control;

class Util {

	/**
	 * Check wether we can verify a certificate using our local store. If not,
	 * assume it is self-signed.
	 *
	 * @param string $endpoint
	 * @return bool
	 */
	public static function is_self_signed($endpoint) : bool {
		$scheme = parse_url($endpoint, PHP_URL_SCHEME);

		if ($scheme === 'http') {
			return false;
		}

		$host = parse_url($endpoint, PHP_URL_HOST);
		$port = parse_url($endpoint, PHP_URL_PORT);

		if ($port === null) {
			$port = 443;
		}

		// Temporarily suppress errors, as stream_socket_client() will always issue a WARNING on failure
		set_error_handler(function(){return true;});
		$socket_client = stream_socket_client('ssl://' . $host . ':' . $port, $errno, $errstr, 5, STREAM_CLIENT_CONNECT);
		restore_error_handler();

		if ($socket_client !== false) {
			stream_socket_shutdown($socket_client, STREAM_SHUT_RDWR);
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Get some info about a certificate from the remote host
	 *
	 * @param string $endpoint
	 * @return array
	 */
	public static function get_self_signed_info($endpoint) {
		$scheme = parse_url($endpoint, PHP_URL_SCHEME);
		$host = parse_url($endpoint, PHP_URL_HOST);
		$port = parse_url($endpoint, PHP_URL_PORT);

		if ($port === null) {
			$port = 443;
		}

		// Disable verification while we fetch the certificate, as it is probably
		// self-signed
		$stream_context = stream_context_create ([
			'ssl' => [
				'capture_peer_cert' => true,
				'verify_peer' => false,
				'verify_peer_name' => false,
			]
		]);

		$socket_client = stream_socket_client('ssl://' . $host . ':' . $port, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $stream_context);
		$result = stream_context_get_params($socket_client);
		$x509_certificate = openssl_x509_parse($result["options"]["ssl"]["peer_certificate"]);

		openssl_x509_export($result["options"]["ssl"]["peer_certificate"], $certificate);

		return [
			'ssl_hostname' => $x509_certificate['subject']['CN'],
			'ssl_certificate' => $certificate,
		];
	}

	/**
	 * Get some info about a certificate
	 *
	 * @param string $certificate
	 * @return array
	 */
	public static function get_certificate_info($certificate) {
		$x509_certificate = openssl_x509_parse($certificate);

		return [
			'serial_number' => $x509_certificate['serialNumberHex'],
			'signature_type' => $x509_certificate['signatureTypeLN'],
			'subject' => $x509_certificate['subject']['CN'],
			'valid_from' => date('r', $x509_certificate['validFrom_time_t']),
			'valid_to' => date('r', $x509_certificate['validTo_time_t']),
		];
	}
}
