<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Responses\License;

use StellarWP\LicensingApiClient\Responses\Contracts\Response;
use StellarWP\LicensingApiClient\Responses\License\ValueObjects\LicenseSummary;
use StellarWP\LicensingApiClient\Responses\License\ValueObjects\ProductValidation;

/**
 * Represents the batch license validation response payload.
 *
 * @implements Response<array{
 *     license: array{key: string, status: string}|null,
 *     domain: string,
 *     is_production: bool,
 *     products: list<array{
 *         product_slug: string,
 *         status: string,
 *         is_valid: bool,
 *         entitlement: array{
 *             tier: string,
 *             site_limit: int,
 *             expiration_date: string,
 *             status: string,
 *             capabilities: list<string>
 *         }|null,
 *         activation: array{
 *             domain: string,
 *             activated_at: string
 *         }|null,
 *         available_entitlements: list<array{
 *             tier: string,
 *             site_limit: int,
 *             active_count: int,
 *             available: int,
 *             capabilities: list<string>,
 *             status: string,
 *             expires: string
 *         }>
 *     }>
 * }>
 */
final class Validate implements Response
{
	public ?LicenseSummary $license;

	public string $domain;

	public bool $isProduction;

	/** @var list<ProductValidation> */
	public array $products;

	/**
	 * @param list<ProductValidation> $products
	 */
	private function __construct(
		?LicenseSummary $license,
		string $domain,
		bool $isProduction,
		array $products = []
	) {
		$this->license      = $license;
		$this->domain       = $domain;
		$this->isProduction = $isProduction;
		$this->products     = $products;
	}

	/**
	 * @param array{
	 *     license: array{key: string, status: string}|null,
	 *     domain: string,
	 *     is_production: bool,
	 *     products: list<array{
	 *         product_slug: string,
	 *         status: string,
	 *         is_valid: bool,
	 *         entitlement: array{
	 *             tier: string,
	 *             site_limit: int,
	 *             expiration_date: string,
	 *             status: string,
	 *             capabilities: list<string>
	 *         }|null,
	 *         activation: array{
	 *             domain: string,
	 *             activated_at: string
	 *         }|null,
	 *         available_entitlements?: list<array{
	 *             tier: string,
	 *             site_limit: int,
	 *             active_count: int,
	 *             available: int,
	 *             capabilities: list<string>,
	 *             status: string,
	 *             expires: string
	 *         }>
	 *     }>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		$license  = $attributes['license'] ?? null;
		$products = array_map(
			static fn(array $product): ProductValidation => ProductValidation::from($product),
			$attributes['products']
		);

		return new self(
			$license ? LicenseSummary::from($license) : null,
			$attributes['domain'],
			$attributes['is_production'],
			$products
		);
	}

	public function toArray(): array {
		return [
			'license'       => $this->license ? $this->license->toArray() : null,
			'domain'        => $this->domain,
			'is_production' => $this->isProduction,
			'products'      => array_map(
				static fn (ProductValidation $product): array => $product->toArray(),
				$this->products
			),
		];
	}
}
