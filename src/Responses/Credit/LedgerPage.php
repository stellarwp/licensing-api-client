<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\Credit;

use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects\LedgerEntry;
use LiquidWeb\LicensingApiClient\Responses\ValueObjects\PageMeta;
use LiquidWeb\LicensingApiClient\Responses\ValueObjects\PaginationLinks;

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
final class LedgerPage implements Response
{
	/**
	 * @var list<LedgerEntry>
	 */
	public array $entries;

	public PaginationLinks $links;

	public PageMeta $page;

	/**
	 * @param list<LedgerEntry> $entries
	 */
	private function __construct(array $entries, PaginationLinks $links, PageMeta $page) {
		$this->entries = $entries;
		$this->links   = $links;
		$this->page    = $page;
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
			array_map(
				static fn (array $entry): LedgerEntry => LedgerEntry::from($entry),
				$attributes['entries']
			),
			PaginationLinks::from($attributes['links']),
			PageMeta::from($attributes['meta']['page'])
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
			'entries' => array_map(
				static fn (LedgerEntry $entry): array => $entry->toArray(),
				$this->entries
			),
			'links'   => $this->links->toArray(),
			'meta'    => [
				'page' => $this->page->toArray(),
			],
		];
	}
}
