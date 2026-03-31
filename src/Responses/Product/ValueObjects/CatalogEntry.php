<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\Product\ValueObjects;

use DateTimeImmutable;
use LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use LiquidWeb\LicensingApiClient\Responses\ValueObjects\CapabilityCollection;

/**
 * Represents one entitlement entry in the product catalog.
 *
 * @implements Response<array{
 *     product_slug: string,
 *     tier: string,
 *     status: string,
 *     expires: string,
 *     capabilities: list<string>,
 *     activations: array{
 *         site_limit: int,
 *         active_count: int,
 *         over_limit: bool,
 *         domains: list<string>
 *     },
 *     activated_here?: bool,
 *     validation_status?: string,
 *     is_valid?: bool
 * }>
 */
final class CatalogEntry implements Response
{
	use InteractsWithDateTime;

	public string $productSlug;

	public string $tier;

	public string $status;

	public DateTimeImmutable $expires;

	public CapabilityCollection $capabilities;

	public Activations $activations;

	public ?bool $activatedHere;

	public ?string $validationStatus;

	public ?bool $isValid;

	/**
	 */
	private function __construct(
		string $productSlug,
		string $tier,
		string $status,
		DateTimeImmutable $expires,
		CapabilityCollection $capabilities,
		Activations $activations,
		?bool $activatedHere = null,
		?string $validationStatus = null,
		?bool $isValid = null
	) {
		$this->productSlug      = $productSlug;
		$this->tier             = $tier;
		$this->status           = $status;
		$this->expires          = $expires;
		$this->capabilities     = $capabilities;
		$this->activations      = $activations;
		$this->activatedHere    = $activatedHere;
		$this->validationStatus = $validationStatus;
		$this->isValid          = $isValid;
	}

	/**
	 * @param array{
	 *     product_slug: string,
	 *     tier: string,
	 *     status: string,
	 *     expires: string,
	 *     capabilities: list<string>,
	 *     activations: array{
	 *         site_limit: int,
	 *         active_count: int,
	 *         over_limit: bool,
	 *         domains: list<string>
	 *     },
	 *     activated_here?: bool,
	 *     validation_status?: string,
	 *     is_valid?: bool
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			$attributes['product_slug'],
			$attributes['tier'],
			$attributes['status'],
			self::parseDateTime($attributes['expires']),
			CapabilityCollection::from($attributes['capabilities']),
			Activations::from($attributes['activations']),
			$attributes['activated_here'] ?? null,
			$attributes['validation_status'] ?? null,
			$attributes['is_valid'] ?? null
		);
	}

	public function toArray(): array {
		return array_merge([
			'product_slug' => $this->productSlug,
			'tier'         => $this->tier,
			'status'       => $this->status,
			'expires'      => $this->expires->format('Y-m-d H:i:s'),
			'capabilities' => $this->capabilities->toArray(),
			'activations'  => $this->activations->toArray(),
		], array_filter([
			'activated_here'    => $this->activatedHere,
			'validation_status' => $this->validationStatus,
			'is_valid'          => $this->isValid,
		], static fn ($value): bool => $value !== null));
	}

	public function isActive(): bool {
		return $this->status === 'active';
	}

	/**
	 * Determine whether this catalog entry was evaluated against a specific site domain.
	 */
	public function hasCurrentSiteValidation(): bool {
		return $this->validationStatus !== null;
	}

	public function isValidForCurrentSite(): bool {
		return $this->hasCurrentSiteValidation()
			&& $this->isValid       === true
			&& $this->activatedHere === true;
	}

	public function hasCapability(string $capability): bool {
		return $this->capabilities->has($capability);
	}

	public function isCapabilityValid(string $capability): bool {
		return $this->isActive()
			&& $this->isValidForCurrentSite()
			&& $this->hasCapability($capability);
	}
}
