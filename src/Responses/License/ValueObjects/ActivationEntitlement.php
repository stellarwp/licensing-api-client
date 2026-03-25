<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\License\ValueObjects;

use DateTimeImmutable;
use LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use LiquidWeb\LicensingApiClient\Responses\ValueObjects\CapabilityCollection;

/**
 * Represents the entitlement returned by a license activation.
 *
 * @implements Response<array{
 *     product_slug: string,
 *     tier: string,
 *     site_limit: int,
 *     expiration_date: string,
 *     status: string,
 *     capabilities: list<string>
 * }>
 */
final class ActivationEntitlement implements Response
{
	use InteractsWithDateTime;

	public string $productSlug;

	public string $tier;

	public int $siteLimit;

	public DateTimeImmutable $expirationDate;

	public string $status;

	public CapabilityCollection $capabilities;

	private function __construct(
		string $productSlug,
		string $tier,
		int $siteLimit,
		DateTimeImmutable $expirationDate,
		string $status,
		CapabilityCollection $capabilities
	) {
		$this->productSlug    = $productSlug;
		$this->tier           = $tier;
		$this->siteLimit      = $siteLimit;
		$this->expirationDate = $expirationDate;
		$this->status         = $status;
		$this->capabilities   = $capabilities;
	}

	/**
	 * @param array{
	 *     product_slug: string,
	 *     tier: string,
	 *     site_limit: int,
	 *     expiration_date: string,
	 *     status: string,
	 *     capabilities: list<string>
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['product_slug'],
			$attributes['tier'],
			$attributes['site_limit'],
			self::parseDateTime($attributes['expiration_date']),
			$attributes['status'],
			CapabilityCollection::from($attributes['capabilities'])
		);
	}

	/**
	 * @return array{
	 *     product_slug: string,
	 *     tier: string,
	 *     site_limit: int,
	 *     expiration_date: string,
	 *     status: string,
	 *     capabilities: list<string>
	 * }
	 */
	public function toArray(): array {
		return [
			'product_slug'    => $this->productSlug,
			'tier'            => $this->tier,
			'site_limit'      => $this->siteLimit,
			'expiration_date' => $this->expirationDate->format('Y-m-d H:i:s'),
			'status'          => $this->status,
			'capabilities'    => $this->capabilities->toArray(),
		];
	}
}
