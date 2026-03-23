<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Responses\License\Listing;

use StellarWP\LicensingApiClient\Responses\Contracts\Response;
use StellarWP\LicensingApiClient\Responses\License\Listing\ValueObjects\LicenseListItem;
use StellarWP\LicensingApiClient\Responses\ValueObjects\PageMeta;
use StellarWP\LicensingApiClient\Responses\ValueObjects\PaginationLinks;

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
 *     links: array{
 *         first: string,
 *         last: string|null,
 *         prev: string|null,
 *         next: string|null
 *     },
 *     meta: array{
 *         page: array{
 *             total: int,
 *             limit: int,
 *             max_size: int
 *         }
 *     }
 * }>
 */
final class Listing implements Response
{
	/**
	 * @var LicenseListItem[]
	 */
	public array $licenses;

	public PaginationLinks $links;

	public PageMeta $page;

	/**
	 * @param LicenseListItem[] $licenses
	 */
	private function __construct(array $licenses, PaginationLinks $links, PageMeta $page) {
		$this->licenses = $licenses;
		$this->links    = $links;
		$this->page     = $page;
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
	 *     links: array{
	 *         first: string,
	 *         last: string|null,
	 *         prev: string|null,
	 *         next: string|null
	 *     },
	 *     meta: array{
	 *         page: array{
	 *             total: int,
	 *             limit: int,
	 *             max_size: int
	 *         }
	 *     }
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			array_map(static fn (array $license): LicenseListItem => LicenseListItem::from($license), $attributes['licenses']),
			PaginationLinks::from($attributes['links']),
			PageMeta::from($attributes['meta']['page'])
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
	 *     links: array{
	 *         first: string,
	 *         last: string|null,
	 *         prev: string|null,
	 *         next: string|null
	 *     },
	 *     meta: array{
	 *         page: array{
	 *             total: int,
	 *             limit: int,
	 *             max_size: int
	 *         }
	 *     }
	 * }
	 */
	public function toArray(): array {
		return [
			'licenses' => array_map(
				static fn (LicenseListItem $license): array => $license->toArray(),
				$this->licenses
			),
			'links'    => $this->links->toArray(),
			'meta'     => [
				'page' => $this->page->toArray(),
			],
		];
	}
}
