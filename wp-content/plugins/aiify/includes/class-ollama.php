<?php

namespace AIIFY;

/**
 * Should we consider trying to keep the model up by sending empty prompts?
 */

/**
 * This class is a fast adaptation of orhanerday/open-ai to Ollama
 */
class Ollama {
	protected $baseUrl = '';
	private $stream_method;
	private $headers;
	private $timeout      = 0;
	private $curlInfo     = array();
	private $contentTypes = array();

	public function __construct( $ollama_url ) {
		$this->contentTypes = array(
			'application/json'    => 'Content-Type: application/json',
			'multipart/form-data' => 'Content-Type: multipart/form-data',
		);
		$this->headers      = array(
			$this->contentTypes['application/json'],
		);
		$this->baseUrl      = $ollama_url;

	}


	/**
	 * Chat request
	 *
	 * @param  $opts
	 * @param  null $stream
	 * @return bool|string
	 * @throws Exception
	 */
	public function chat( $opts, $stream = null ) {
		if ( $stream != null && array_key_exists( 'stream', $opts ) ) {
			if ( ! $opts['stream'] ) {
				throw new Exception(
					'Please provide a stream function.'
				);
			}

			$this->stream_method = $stream;
		}

		return $this->sendRequest( $this->baseUrl . '/api/chat', 'POST', $opts );
	}

	/**
	 * Retrieves models
	 *
	 * @param      callable|null $mapper  The mapper
	 *
	 * @return     array               ( description_of_the_return_value )
	 */
	public function listModels( ?callable $mapper = null ) {
		$models = $this->sendRequest( $this->baseUrl . '/api/tags', 'GET' );
		$models = json_decode( $models, true );
		$models = isset( $models['models'] ) ? $models['models'] : null;
		$mapped = array();
		foreach ( $models as $model ) {
			$mapped[ $model['name'] ] = is_callable( $mapper ) ? $mapper( $model ) : $model;
		}
		return $mapped;
	}

	/**
	 * @param  string $url
	 * @param  string $method
	 * @param  array  $opts
	 * @return bool|string
	 */
	private function sendRequest( string $url, string $method, array $opts = array() ) {
		$post_fields = json_encode( $opts );

		if ( array_key_exists( 'file', $opts ) || array_key_exists( 'image', $opts ) ) {
			$this->headers[0] = $this->contentTypes['multipart/form-data'];
			$post_fields      = $opts;
		} else {
			$this->headers[0] = $this->contentTypes['application/json'];
		}
		$curl_info = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => $this->timeout,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_POSTFIELDS     => $post_fields,
			CURLOPT_HTTPHEADER     => $this->headers,
		);

		if ( $opts == array() ) {
			unset( $curl_info[ CURLOPT_POSTFIELDS ] );
		}

		if ( ! empty( $this->proxy ) ) {
			$curl_info[ CURLOPT_PROXY ] = $this->proxy;
		}

		if ( array_key_exists( 'stream', $opts ) && $opts['stream'] ) {
			$curl_info[ CURLOPT_WRITEFUNCTION ] = $this->stream_method;
		}

		$curl = curl_init();

		curl_setopt_array( $curl, $curl_info );
		$response = curl_exec( $curl );

		$info           = curl_getinfo( $curl );
		$this->curlInfo = $info;

		curl_close( $curl );

		if ( ! $response ) {
			throw new Exception( curl_error( $curl ) );
		}

		return $response;
	}
}
