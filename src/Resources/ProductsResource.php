<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use StellarWP\LicensingApiClient\Exceptions\MissingAuthenticationException;
use StellarWP\LicensingApiClient\Exceptions\UnexpectedResponseException;
use StellarWP\LicensingApiClient\Http\AuthState;
use StellarWP\LicensingApiClient\Http\Factories\EndpointFactory;
use StellarWP\LicensingApiClient\Http\RequestExecutor;
use StellarWP\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use StellarWP\LicensingApiClient\Resources\Contracts\ProductsResourceInterface;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;
use StellarWP\LicensingApiClient\Responses\Product\Catalog;

/**
 * Provides operations for the products API resource.
 *
 * @phpstan-type CatalogPayload array{
 *     products: list<array{
 *         product_slug: string,
 *         tier: string,
 *         status: string,
 *         expires: string,
 *         capabilities: list<string>,
 *         activations: array{
 *             site_limit: int,
 *             active_count: int,
 *             over_limit: bool,
 *             domains: list<string>
 *         },
 *         activated_here?: bool,
 *         validation_status?: string,
 *         is_valid?: bool
 *     }>
 * }
 */
final class ProductsResource implements ProductsResourceInterface
{
	use RebindsAuthState;

	private RequestExecutor $requestExecutor;

	private EndpointFactory $endpointFactory;

	private AuthState $authState;

	public function __construct(
		RequestExecutor $requestExecutor,
		EndpointFactory $endpointFactory,
		AuthState $authState
	) {
		$this->requestExecutor = $requestExecutor;
		$this->endpointFactory = $endpointFactory;
		$this->authState       = $authState;
	}

	protected function rebindWithAuthState(AuthState $authState): self {
		return new self($this->requestExecutor, $this->endpointFactory, $authState);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Catalog|ErrorResponse
	 */
	public function catalog(string $key, ?string $domain = null) {
		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->endpointFactory->make('/products'),
			array_filter([
				'key'    => $key,
				'domain' => $domain,
			], static fn($value): bool => $value !== null),
			null,
			$this->authState->resolveTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var CatalogPayload $result */
		return Catalog::from($result);
	}
}
