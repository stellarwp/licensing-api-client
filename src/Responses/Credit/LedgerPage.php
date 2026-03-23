<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Responses\Credit;

use StellarWP\LicensingApiClient\Responses\Contracts\Response;
use StellarWP\LicensingApiClient\Responses\Credit\ValueObjects\LedgerEntry;

/**
 * Represents a cursor-paginated credits ledger response.
 *
 * @implements Response<array{
 *     entries: list<array{
 *         id: int,
 *         pool_id: int,
 *         activation_id: int,
 *         domain: string,
 *         product_slug: string,
 *         credit_type: string,
 *         entry_type: string,
 *         amount: int,
 *         period_start: string|null,
 *         idempotency_key: string,
 *         created_at: string
 *     }>,
 *     limit: int,
 *     next_cursor: int|null
 * }>
 */
final class LedgerPage implements Response
{
	/**
	 * @var list<LedgerEntry>
	 */
	public array $entries;

	public int $limit;

	public ?int $nextCursor;

	/**
	 * @param list<LedgerEntry> $entries
	 */
	private function __construct(array $entries, int $limit, ?int $nextCursor) {
		$this->entries     = $entries;
		$this->limit       = $limit;
		$this->nextCursor  = $nextCursor;
	}

	/**
	 * @param array{
	 *     entries: list<array{
	 *         id: int,
	 *         pool_id: int,
	 *         activation_id: int,
	 *         domain: string,
	 *         product_slug: string,
	 *         credit_type: string,
	 *         entry_type: string,
	 *         amount: int,
	 *         period_start: string|null,
	 *         idempotency_key: string,
	 *         created_at: string
	 *     }>,
	 *     limit: int,
	 *     next_cursor: int|null
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			array_map(
				static fn (array $entry): LedgerEntry => LedgerEntry::from($entry),
				$attributes['entries']
			),
			$attributes['limit'],
			$attributes['next_cursor']
		);
	}

	/**
	 * @return array{
	 *     entries: list<array{
	 *         id: int,
	 *         pool_id: int,
	 *         activation_id: int,
	 *         domain: string,
	 *         product_slug: string,
	 *         credit_type: string,
	 *         entry_type: string,
	 *         amount: int,
	 *         period_start: string|null,
	 *         idempotency_key: string,
	 *         created_at: string
	 *     }>,
	 *     limit: int,
	 *     next_cursor: int|null
	 * }
	 */
	public function toArray(): array {
		return [
			'entries' => array_map(
				static fn (LedgerEntry $entry): array => $entry->toArray(),
				$this->entries
			),
			'limit'       => $this->limit,
			'next_cursor' => $this->nextCursor,
		];
	}
}
