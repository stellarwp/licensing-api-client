<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\License\ValueObjects;

use DateTimeImmutable;
use LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use LiquidWeb\LicensingApiClient\Responses\ValueObjects\CapabilityCollection;

/**
 * Represents an entitlement option when tier selection is required.
 *
 * @implements Response<array{
 *     tier: string,
 *     site_limit: int,
 *     active_count: int,
 *     available: int,
 *     capabilities: list<string>,
 *     status: string,
 *     expires: string
 * }>
 */
final class AvailableEntitlement implements Response
{
	use InteractsWithDateTime;

	public string $tier;

	public int $siteLimit;

	public int $activeCount;

	public int $available;

	public CapabilityCollection $capabilities;

	public string $status;

	public DateTimeImmutable $expires;

	private function __construct(
		string $tier,
		int $siteLimit,
		int $activeCount,
		int $available,
		CapabilityCollection $capabilities,
		string $status,
		DateTimeImmutable $expires
	) {
		$this->tier         = $tier;
		$this->siteLimit    = $siteLimit;
		$this->activeCount  = $activeCount;
		$this->available    = $available;
		$this->capabilities = $capabilities;
		$this->status       = $status;
		$this->expires      = $expires;
	}

	/**
	 * @param array{
	 *     tier: string,
	 *     site_limit: int,
	 *     active_count: int,
	 *     available: int,
	 *     capabilities: list<string>,
	 *     status: string,
	 *     expires: string
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['tier'],
			$attributes['site_limit'],
			$attributes['active_count'],
			$attributes['available'],
			CapabilityCollection::from($attributes['capabilities']),
			$attributes['status'],
			self::parseDateTime($attributes['expires'])
		);
	}

	public function toArray(): array {
		return [
			'tier'         => $this->tier,
			'site_limit'   => $this->siteLimit,
			'active_count' => $this->activeCount,
			'available'    => $this->available,
			'capabilities' => $this->capabilities->toArray(),
			'status'       => $this->status,
			'expires'      => $this->formatDateTime($this->expires),
		];
	}
}
