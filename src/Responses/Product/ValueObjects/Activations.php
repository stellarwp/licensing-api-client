<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Responses\Product\ValueObjects;

use StellarWP\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents activation count details for one catalog entry.
 *
 * @implements Response<array{
 *     site_limit: int,
 *     active_count: int,
 *     over_limit: bool,
 *     domains: list<string>
 * }>
 */
final class Activations implements Response
{
	public int $siteLimit;

	public int $activeCount;

	public bool $overLimit;

	/** @var list<string> */
	public array $domains;

	/**
	 * @param list<string> $domains
	 */
	private function __construct(
		int $siteLimit,
		int $activeCount,
		bool $overLimit,
		array $domains
	) {
		$this->siteLimit   = $siteLimit;
		$this->activeCount = $activeCount;
		$this->overLimit   = $overLimit;
		$this->domains     = $domains;
	}

	/**
	 * @param array{
	 *     site_limit: int,
	 *     active_count: int,
	 *     over_limit: bool,
	 *     domains: list<string>
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['site_limit'],
			$attributes['active_count'],
			$attributes['over_limit'],
			$attributes['domains']
		);
	}

	public function toArray(): array {
		return [
			'site_limit'   => $this->siteLimit,
			'active_count' => $this->activeCount,
			'over_limit'   => $this->overLimit,
			'domains'      => $this->domains,
		];
	}
}
