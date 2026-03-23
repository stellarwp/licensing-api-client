<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Responses\Credit\ValueObjects;

use DateTimeImmutable;
use StellarWP\LicensingApiClient\Concerns\InteractsWithDateTime;
use StellarWP\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents one credits ledger entry.
 *
 * @implements Response<array{
 *     id: int,
 *     pool_id: int,
 *     activation_id: int,
 *     domain: string,
 *     product_slug: string,
 *     credit_type: string,
 *     entry_type: string,
 *     amount: int,
 *     period_start: string|null,
 *     idempotency_key: string,
 *     created_at: string
 * }>
 */
final class LedgerEntry implements Response
{
	use InteractsWithDateTime;

	public int $id;

	public int $poolId;

	public int $activationId;

	public string $domain;

	public string $productSlug;

	public string $creditType;

	public string $entryType;

	public int $amount;

	public ?DateTimeImmutable $periodStart;

	public string $idempotencyKey;

	public DateTimeImmutable $createdAt;

	private function __construct(
		int $id,
		int $poolId,
		int $activationId,
		string $domain,
		string $productSlug,
		string $creditType,
		string $entryType,
		int $amount,
		?DateTimeImmutable $periodStart,
		string $idempotencyKey,
		DateTimeImmutable $createdAt
	) {
		$this->id             = $id;
		$this->poolId         = $poolId;
		$this->activationId   = $activationId;
		$this->domain         = $domain;
		$this->productSlug    = $productSlug;
		$this->creditType     = $creditType;
		$this->entryType      = $entryType;
		$this->amount         = $amount;
		$this->periodStart    = $periodStart;
		$this->idempotencyKey = $idempotencyKey;
		$this->createdAt      = $createdAt;
	}

	/**
	 * @param array{
	 *     id: int,
	 *     pool_id: int,
	 *     activation_id: int,
	 *     domain: string,
	 *     product_slug: string,
	 *     credit_type: string,
	 *     entry_type: string,
	 *     amount: int,
	 *     period_start: string|null,
	 *     idempotency_key: string,
	 *     created_at: string
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['id'],
			$attributes['pool_id'],
			$attributes['activation_id'],
			$attributes['domain'],
			$attributes['product_slug'],
			$attributes['credit_type'],
			$attributes['entry_type'],
			$attributes['amount'],
			$attributes['period_start'] ? self::parseDateTime($attributes['period_start']) : null,
			$attributes['idempotency_key'],
			self::parseDateTime($attributes['created_at'])
		);
	}

	/**
	 * @return array{
	 *     id: int,
	 *     pool_id: int,
	 *     activation_id: int,
	 *     domain: string,
	 *     product_slug: string,
	 *     credit_type: string,
	 *     entry_type: string,
	 *     amount: int,
	 *     period_start: string|null,
	 *     idempotency_key: string,
	 *     created_at: string
	 * }
	 */
	public function toArray(): array {
		return [
			'id'              => $this->id,
			'pool_id'         => $this->poolId,
			'activation_id'   => $this->activationId,
			'domain'          => $this->domain,
			'product_slug'    => $this->productSlug,
			'credit_type'     => $this->creditType,
			'entry_type'      => $this->entryType,
			'amount'          => $this->amount,
			'period_start'    => $this->periodStart ? $this->periodStart->format('Y-m-d\TH:i:s\Z') : null,
			'idempotency_key' => $this->idempotencyKey,
			'created_at'      => $this->createdAt->format('Y-m-d\TH:i:s\Z'),
		];
	}
}
