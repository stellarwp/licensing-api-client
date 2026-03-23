<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Requests\License\Listing;

/**
 * Defines the query parameters for listing licenses.
 *
 * Filters:
 * - `search` performs a broad text search across listable license fields.
 * - `identityId` narrows results to a specific external customer identity.
 * - `status` applies the server-side license status filter.
 * - `productSlug` and `tier` limit results to matching subscriptions.
 *
 * Cursor pagination:
 * - `beforeId` is the next cursor value returned by the previous page.
 * - `limit` controls the page size and defaults to the API default of 25.
 *
 * @TODO Update once we add back and forward cursor pagination.
 */
final class ListRequest
{
	public ?string $search;

	public ?string $identityId;

	public ?string $status;

	public ?string $productSlug;

	public ?string $tier;

	public ?int $beforeId;

	public int $limit;

	public function __construct(
		?string $search = null,
		?string $identityId = null,
		?string $status = null,
		?string $productSlug = null,
		?string $tier = null,
		?int $beforeId = null,
		int $limit = 25
	) {
		$this->search      = $search;
		$this->identityId  = $identityId;
		$this->status      = $status;
		$this->productSlug = $productSlug;
		$this->tier        = $tier;
		$this->beforeId    = $beforeId;
		$this->limit       = $limit;
	}

	/**
	 * @return array{
	 *     search?: string,
	 *     identity_id?: string,
	 *     status?: string,
	 *     product_slug?: string,
	 *     tier?: string,
	 *     before_id?: int,
	 *     limit: int
	 * }
	 */
	public function toQuery(): array {
		return array_filter([
			'search'       => $this->search,
			'identity_id'  => $this->identityId,
			'status'       => $this->status,
			'product_slug' => $this->productSlug,
			'tier'         => $this->tier,
			'before_id'    => $this->beforeId,
			'limit'        => $this->limit,
		], static fn ($value): bool => $value !== null);
	}
}
