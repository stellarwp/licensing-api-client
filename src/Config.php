<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient;

use InvalidArgumentException;
use StellarWP\LicensingApiClient\Http\RetryPolicy;
use StellarWP\LicensingApiClient\Value\AuthToken;

/**
 * Stores stable client configuration shared across API requests.
 */
final class Config
{
	public string $baseUri;

	public ?AuthToken $configuredToken;

	public string $userAgent;

	public RetryPolicy $retryPolicy;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(
		string $baseUri,
		?string $configuredToken = null,
		?string $userAgent = null,
		?RetryPolicy $retryPolicy = null
	) {
		$baseUri = rtrim($baseUri, '/');

		if ($baseUri === '') {
			throw new InvalidArgumentException('Base URI cannot be empty.');
		}

		$this->baseUri         = $baseUri;
		$this->configuredToken = $configuredToken !== null ? new AuthToken($configuredToken) : null;
		$this->userAgent       = $userAgent !== null && $userAgent !== '' ? $userAgent : 'stellarwp/licensing-api-client';
		$this->retryPolicy     = $retryPolicy ?: RetryPolicy::default();
	}

	/**
	 * @param array{
	 *     base_uri?: non-empty-string,
	 *     configured_token?: non-empty-string|null,
	 *     user_agent?: non-empty-string|null,
	 *     retry_policy?: RetryPolicy|null
	 * } $config
	 *
	 * @throws InvalidArgumentException
	 */
	public static function fromArray(array $config): self {
		return new self(
			$config['base_uri'] ?? '',
			$config['configured_token'] ?? null,
			$config['user_agent'] ?? null,
			$config['retry_policy'] ?? null
		);
	}
}
