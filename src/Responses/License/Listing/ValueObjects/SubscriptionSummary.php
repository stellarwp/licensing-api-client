<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\License\Listing\ValueObjects;

use DateTimeImmutable;
use LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a subscription summary in a license listing.
 *
 * @implements Response<array{
 *     product_slug: string,
 *     tier: string,
 *     site_limit: int,
 *     active_count: int,
 *     status: string,
 *     expiration_date: string,
 *     purchase_date: string
 * }>
 */
final class SubscriptionSummary implements Response
{
	use InteractsWithDateTime;

	public string $productSlug;

	public string $tier;

	public int $siteLimit;

	public int $activeCount;

	public string $status;

	public DateTimeImmutable $expirationDate;

	public DateTimeImmutable $purchaseDate;

	private function __construct(
		string $productSlug,
		string $tier,
		int $siteLimit,
		int $activeCount,
		string $status,
		DateTimeImmutable $expirationDate,
		DateTimeImmutable $purchaseDate
	) {
		$this->productSlug    = $productSlug;
		$this->tier           = $tier;
		$this->siteLimit      = $siteLimit;
		$this->activeCount    = $activeCount;
		$this->status         = $status;
		$this->expirationDate = $expirationDate;
		$this->purchaseDate   = $purchaseDate;
	}

	/**
	 * @param array{
	 *     product_slug: string,
	 *     tier: string,
	 *     site_limit: int,
	 *     active_count: int,
	 *     status: string,
	 *     expiration_date: string,
	 *     purchase_date: string
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['product_slug'],
			$attributes['tier'],
			$attributes['site_limit'],
			$attributes['active_count'],
			$attributes['status'],
			self::parseDateTime($attributes['expiration_date']),
			self::parseDateTime($attributes['purchase_date'])
		);
	}

	/**
	 * @return array{
	 *     product_slug: string,
	 *     tier: string,
	 *     site_limit: int,
	 *     active_count: int,
	 *     status: string,
	 *     expiration_date: string,
	 *     purchase_date: string
	 * }
	 */
	public function toArray(): array {
		return [
			'product_slug'    => $this->productSlug,
			'tier'            => $this->tier,
			'site_limit'      => $this->siteLimit,
			'active_count'    => $this->activeCount,
			'status'          => $this->status,
			'expiration_date' => $this->expirationDate->format('Y-m-d H:i:s'),
			'purchase_date'   => $this->purchaseDate->format('Y-m-d H:i:s'),
		];
	}
}
