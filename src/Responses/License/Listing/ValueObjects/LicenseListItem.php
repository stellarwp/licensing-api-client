<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\License\Listing\ValueObjects;

use DateTimeImmutable;
use LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a single license in a cursor-paginated listing.
 *
 * @implements Response<array{
 *     license_key: string,
 *     identity_id: string,
 *     status: string,
 *     created_at: string,
 *     updated_at: string,
 *     subscriptions: list<array{
 *         product_slug: string,
 *         tier: string,
 *         site_limit: int,
 *         active_count: int,
 *         status: string,
 *         expiration_date: string,
 *         purchase_date: string
 *     }>
 * }>
 */
final class LicenseListItem implements Response
{
	use InteractsWithDateTime;

	public string $licenseKey;

	public string $identityId;

	public string $status;

	public DateTimeImmutable $createdAt;

	public DateTimeImmutable $updatedAt;

	/**
	 * @var SubscriptionSummary[]
	 */
	public array $subscriptions;

	/**
	 * @param SubscriptionSummary[] $subscriptions
	 */
	private function __construct(
		string $licenseKey,
		string $identityId,
		string $status,
		DateTimeImmutable $createdAt,
		DateTimeImmutable $updatedAt,
		array $subscriptions
	) {
		$this->licenseKey    = $licenseKey;
		$this->identityId    = $identityId;
		$this->status        = $status;
		$this->createdAt     = $createdAt;
		$this->updatedAt     = $updatedAt;
		$this->subscriptions = $subscriptions;
	}

	/**
	 * @param array{
	 *     license_key: string,
	 *     identity_id: string,
	 *     status: string,
	 *     created_at: string,
	 *     updated_at: string,
	 *     subscriptions: list<array{
	 *         product_slug: string,
	 *         tier: string,
	 *         site_limit: int,
	 *         active_count: int,
	 *         status: string,
	 *         expiration_date: string,
	 *         purchase_date: string
	 *     }>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['license_key'],
			$attributes['identity_id'],
			$attributes['status'],
			self::parseDateTime($attributes['created_at']),
			self::parseDateTime($attributes['updated_at']),
			array_map(
				static fn (array $subscription): SubscriptionSummary => SubscriptionSummary::from($subscription),
				$attributes['subscriptions']
			)
		);
	}

	/**
	 * @return array{
	 *     license_key: string,
	 *     identity_id: string,
	 *     status: string,
	 *     created_at: string,
	 *     updated_at: string,
	 *     subscriptions: list<array{
	 *         product_slug: string,
	 *         tier: string,
	 *         site_limit: int,
	 *         active_count: int,
	 *         status: string,
	 *         expiration_date: string,
	 *         purchase_date: string
	 *     }>
	 * }
	 */
	public function toArray(): array {
		return [
			'license_key'   => $this->licenseKey,
			'identity_id'   => $this->identityId,
			'status'        => $this->status,
			'created_at'    => $this->createdAt->format('Y-m-d H:i:s'),
			'updated_at'    => $this->updatedAt->format('Y-m-d H:i:s'),
			'subscriptions' => array_map(
				static fn (SubscriptionSummary $subscription): array => $subscription->toArray(),
				$this->subscriptions
			),
		];
	}
}
