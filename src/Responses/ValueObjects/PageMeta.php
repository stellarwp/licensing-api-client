<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\ValueObjects;

final class PageMeta
{
	public int $total;

	public int $limit;

	public int $maxSize;

	private function __construct(int $total, int $limit, int $maxSize) {
		$this->total   = $total;
		$this->limit   = $limit;
		$this->maxSize = $maxSize;
	}

	/**
	 * @param array{
	 *     total: int,
	 *     limit: int,
	 *     max_size: int
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['total'],
			$attributes['limit'],
			$attributes['max_size']
		);
	}

	/**
	 * @return array{
	 *     total: int,
	 *     limit: int,
	 *     max_size: int
	 * }
	 */
	public function toArray(): array {
		return [
			'total'    => $this->total,
			'limit'    => $this->limit,
			'max_size' => $this->maxSize,
		];
	}
}
