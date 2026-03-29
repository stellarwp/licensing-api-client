<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources;

use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Http\Factories\ApiUriFactory;
use LiquidWeb\LicensingApiClient\Http\RequestExecutor;
use LiquidWeb\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use LiquidWeb\LicensingApiClient\Resources\Contracts\ProductsResourceInterface;
use LiquidWeb\LicensingApiClient\Responses\Product\Catalog;
use Psr\Http\Client\ClientExceptionInterface;

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

	private ApiUriFactory $apiUriFactory;

	private AuthState $authState;

	public function __construct(
		RequestExecutor $requestExecutor,
		ApiUriFactory $apiUriFactory,
		AuthState $authState
	) {
		$this->requestExecutor = $requestExecutor;
		$this->apiUriFactory   = $apiUriFactory;
		$this->authState       = $authState;
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function catalog(string $licenseKey, ?string $domain = null): Catalog {
		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->apiUriFactory->make('/products'),
			array_filter([
				'license_key' => $licenseKey,
				'domain'      => $domain,
			], static fn($value): bool => $value !== null),
			null,
			$this->authState->optionalToken()
		);

		/** @var CatalogPayload $result */
		return Catalog::from($result);
	}

	protected function rebindWithAuthState(AuthState $authState): self {
		return new self($this->requestExecutor, $this->apiUriFactory, $authState);
	}
}
