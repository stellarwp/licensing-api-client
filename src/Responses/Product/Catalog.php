<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\Product;

use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use LiquidWeb\LicensingApiClient\Responses\Product\ValueObjects\CatalogEntry;

/**
 * Represents the product catalog response payload.
 *
 * @implements Response<array{
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
 * }>
 */
final class Catalog implements Response
{
	/**
	 * @var CatalogEntry[]
	 */
	public array $products;

	/**
	 * @param CatalogEntry[] $products
	 */
	private function __construct(array $products) {
		$this->products = $products;
	}

	/**
	 * @param array{
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
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		$products = array_map(
			static fn (array $entry): CatalogEntry => CatalogEntry::from($entry),
			$attributes['products']
		);

		return new self($products);
	}

	public function toArray(): array {
		return [
			'products' => array_map(
				static fn (CatalogEntry $entry): array => $entry->toArray(),
				$this->products
			),
		];
	}
}
