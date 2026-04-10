<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\Product\ValueObjects;

use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents activation count details for one catalog entry.
 *
 * @implements Response<array{
 *     site_limit: int,
 *     active_count: int,
 *     over_limit: bool,
 *     domains: array<string, array{
 *         activated_at: string,
 *         deactivated_at: string|null,
 *         is_active: bool,
 *         is_production: bool
 *     }>
 * }>
 */
final class Activations implements Response
{
	public int $siteLimit;

	public int $activeCount;

	public bool $overLimit;

	/** @var array<string, ActivationDomain> */
	public array $domains;

	/**
	 * @param array<string, ActivationDomain> $domains
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
	 *     domains: array<string, array{
	 *         activated_at: string,
	 *         deactivated_at: string|null,
	 *         is_active: bool,
	 *         is_production: bool
	 *     }>
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['site_limit'],
			$attributes['active_count'],
			$attributes['over_limit'],
			array_map(
				static fn (array $domain): ActivationDomain => ActivationDomain::from($domain),
				$attributes['domains']
			)
		);
	}

	/**
	 * Return the activation metadata for a specific domain, or null when absent.
	 */
	public function forDomain(string $domain): ?ActivationDomain {
		return $this->domains[$domain] ?? null;
	}

	/**
	 * @return array{
	 *     site_limit: int,
	 *     active_count: int,
	 *     over_limit: bool,
	 *     domains: array<string, array{
	 *         activated_at: string,
	 *         deactivated_at: string|null,
	 *         is_active: bool,
	 *         is_production: bool
	 *     }>
	 * }
	 */
	public function toArray(): array {
		return [
			'site_limit'   => $this->siteLimit,
			'active_count' => $this->activeCount,
			'over_limit'   => $this->overLimit,
			'domains'      => array_map(
				static fn (ActivationDomain $domain): array => $domain->toArray(),
				$this->domains
			),
		];
	}
}
