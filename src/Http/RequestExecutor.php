<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Http;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\ResponseInterface;
use StellarWP\LicensingApiClient\Exceptions\DecodingException;
use StellarWP\LicensingApiClient\Exceptions\UnexpectedResponseException;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;
use StellarWP\LicensingApiClient\Value\AuthToken;

/**
 * Executes API requests and normalizes error responses.
 *
 * @phpstan-import-type QueryValue from RequestBuilder
 * @phpstan-import-type HeaderValue from RequestBuilder
 * @phpstan-import-type JsonObject from RequestBuilder
 * @phpstan-type ErrorPayload array{
 *     error: array{
 *         code: string,
 *         message: string
 *     }
 * }
 */
final class RequestExecutor
{
	private HttpClient $httpClient;

	private RequestBuilder $requestBuilder;

	private JsonDecoder $jsonDecoder;

	public function __construct(
		HttpClient $httpClient,
		RequestBuilder $requestBuilder,
		JsonDecoder $jsonDecoder
	) {
		$this->httpClient     = $httpClient;
		$this->requestBuilder = $requestBuilder;
		$this->jsonDecoder    = $jsonDecoder;
	}

	/**
	 * @param array<string, QueryValue> $query
	 * @param JsonObject|null           $body
	 * @param array<string, HeaderValue> $headers
	 *
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 * @return ResponseInterface|ErrorResponse
	 */
	public function execute(
		string $method,
		Endpoint $endpoint,
		array $query = [],
		?array $body = null,
		?AuthToken $token = null,
		array $headers = []
	) {
		$request    = $this->requestBuilder->build($method, $endpoint, $query, $body, $token, $headers);
		$response   = $this->httpClient->sendRequest($request);
		$statusCode = $response->getStatusCode();

		if ($statusCode >= 200 && $statusCode < 400) {
			return $response;
		}

		if ($statusCode >= 400 && $statusCode < 500) {
			return $this->buildErrorResponse($response);
		}

		$message = $response->getReasonPhrase() ?: 'Server Error';

		throw new UnexpectedResponseException($message, $statusCode);
	}

	/**
	 * @param array<string, QueryValue> $query
	 * @param JsonObject|null           $body
	 * @param array<string, HeaderValue> $headers
	 *
	 * @return array<array-key, mixed>|ErrorResponse
	 *
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function executeJson(
		string $method,
		Endpoint $endpoint,
		array $query = [],
		?array $body = null,
		?AuthToken $token = null,
		array $headers = []
	) {
		$response = $this->execute($method, $endpoint, $query, $body, $token, $headers);

		if ($response instanceof ErrorResponse) {
			return $response;
		}

		$body = (string) $response->getBody();

		try {
			return $this->jsonDecoder->decode($body);
		} catch (DecodingException $exception) {
			throw new UnexpectedResponseException('Unable to decode JSON response.', 0, $exception);
		}
	}
	/**
	 * @throws UnexpectedResponseException
	 */
	private function buildErrorResponse(ResponseInterface $response): ErrorResponse {
		$body = (string) $response->getBody();

		try {
			$decoded = $this->jsonDecoder->decode($body);
		} catch (DecodingException $exception) {
			throw new UnexpectedResponseException('Unable to decode error response.', 0, $exception);
		}

		if ( ! $this->isErrorPayload($decoded)) {
			throw new UnexpectedResponseException('Unexpected error response structure.');
		}

		return ErrorResponse::from($decoded['error']);
	}

	/**
	 * @param array<array-key, mixed>       $payload
	 *
	 * @phpstan-assert-if-true ErrorPayload $payload
	 */
	private function isErrorPayload(array $payload): bool {
		if ( ! isset($payload['error']) || ! is_array($payload['error'])) {
			return false;
		}

		return isset($payload['error']['code'], $payload['error']['message'])
			&& is_string($payload['error']['code'])
			&& is_string($payload['error']['message']);
	}
}
