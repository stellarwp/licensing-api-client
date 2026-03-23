<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Http;

use JsonException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use StellarWP\LicensingApiClient\Config;
use StellarWP\LicensingApiClient\Value\AuthToken;

/**
 * Builds PSR-7 requests from SDK configuration and endpoint input.
 *
 * @phpstan-type QueryValue string|int|float|bool|null
 * @phpstan-type HeaderValue string|int|float|bool
 * @phpstan-type JsonScalar string|int|float|bool|null
 * @phpstan-type JsonCollection array<array-key, JsonScalar|array<array-key, JsonScalar|array<array-key, JsonScalar|null>|null>|null>
 * @phpstan-type JsonObject array<string, JsonScalar|JsonCollection|null>
 */
final class RequestBuilder
{
	private RequestFactoryInterface $requestFactory;

	private StreamFactoryInterface $streamFactory;

	private Config $config;

	public function __construct(
		RequestFactoryInterface $requestFactory,
		StreamFactoryInterface $streamFactory,
		Config $config
	) {
		$this->requestFactory = $requestFactory;
		$this->streamFactory  = $streamFactory;
		$this->config         = $config;
	}

	/**
	 * @param array<string, QueryValue> $query
	 * @param JsonObject|null           $body
	 * @param array<string, HeaderValue> $headers
	 *
	 * @throws JsonException
	 */
	public function build(
		string $method,
		Endpoint $endpoint,
		array $query = [],
		?array $body = null,
		?AuthToken $token = null,
		array $headers = []
	): RequestInterface {
		$request = $this->requestFactory->createRequest(
			$method,
			$this->buildUri($endpoint, $query)
		);

		if ($token !== null) {
			$request = $request->withHeader('X-LWS-Token', $token->value());
		}

		foreach ($headers as $name => $value) {
			$request = $request->withHeader($name, (string) $value);
		}

		if ($body !== null) {
			$request = $request->withHeader('Content-Type', 'application/json');
			$request = $request->withBody(
				$this->streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR))
			);
		}

		return $request;
	}

	/**
	 * @param array<string, QueryValue> $query
	 */
	private function buildUri(Endpoint $endpoint, array $query): string {
		$uri = rtrim($this->config->baseUri, '/')
			. '/wp-json/stellarwp/'
			. $endpoint->version()->value()
			. '/'
			. ltrim($endpoint->path(), '/');

		$queryString = http_build_query(array_filter($query, static function ($value): bool {
			return $value !== null;
		}));

		if ($queryString === '') {
			return $uri;
		}

		return $uri . '?' . $queryString;
	}

	/**
	 * @param array<string, QueryValue> $query
	 */
}
