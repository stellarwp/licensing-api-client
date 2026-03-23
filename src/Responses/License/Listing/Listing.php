<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Responses\License\Listing;

use StellarWP\LicensingApiClient\Responses\Contracts\Response;
use StellarWP\LicensingApiClient\Responses\License\Listing\ValueObjects\LicenseListItem;

/**
 * Represents a cursor-paginated license listing.
 *
 * @implements Response<array{
 *     licenses: list<array{
 *         license_key: string,
 *         identity_id: string,
 *         status: string,
 *         created_at: string,
 *         updated_at: string,
 *         subscriptions: list<array{
 *             product_slug: string,
 *             tier: string,
 *             site_limit: int,
 *             active_count: int,
 *             status: string,
 *             expiration_date: string,
 *             purchase_date: string
 *         }>
 *     }>,
 *     limit: int,
 *     next_cursor: ?int
 * }>
 *
 * @TODO Update once we add back and forward cursor pagination.
 */
final class Listing implements Response
{
	/**
	 * @var LicenseListItem[]
	 */
	public array $licenses;

	public int $limit;

	public ?int $nextCursor;

	/**
	 * @param LicenseListItem[] $licenses
	 */
	private function __construct(array $licenses, int $limit, ?int $nextCursor) {
		$this->licenses   = $licenses;
		$this->limit      = $limit;
		$this->nextCursor = $nextCursor;
	}

	/**
	 * @param array{
	 *     licenses: list<array{
	 *         license_key: string,
	 *         identity_id: string,
	 *         status: string,
	 *         created_at: string,
	 *         updated_at: string,
	 *         subscriptions: list<array{
	 *             product_slug: string,
	 *             tier: string,
	 *             site_limit: int,
	 *             active_count: int,
	 *             status: string,
	 *             expiration_date: string,
	 *             purchase_date: string
	 *         }>
	 *     }>,
	 *     limit: int,
	 *     next_cursor: ?int
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			array_map(static fn (array $license): LicenseListItem => LicenseListItem::from($license), $attributes['licenses']),
			$attributes['limit'],
			$attributes['next_cursor']
		);
	}

	/**
	 * @return array{
	 *     licenses: list<array{
	 *         license_key: string,
	 *         identity_id: string,
	 *         status: string,
	 *         created_at: string,
	 *         updated_at: string,
	 *         subscriptions: list<array{
	 *             product_slug: string,
	 *             tier: string,
	 *             site_limit: int,
	 *             active_count: int,
	 *             status: string,
	 *             expiration_date: string,
	 *             purchase_date: string
	 *         }>
	 *     }>,
	 *     limit: int,
	 *     next_cursor: ?int
	 * }
	 */
	public function toArray(): array {
		return [
			'licenses'    => array_map(
				static fn (LicenseListItem $license): array => $license->toArray(),
				$this->licenses
			),
			'limit'       => $this->limit,
			'next_cursor' => $this->nextCursor,
		];
	}
}
