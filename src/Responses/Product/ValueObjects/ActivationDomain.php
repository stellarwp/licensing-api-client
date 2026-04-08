<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\Product\ValueObjects;

use DateTimeImmutable;
use LiquidWeb\LicensingApiClient\Concerns\InteractsWithDateTime;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents activation metadata for a specific domain.
 *
 * @implements Response<array{
 *     activated_at: string,
 *     deactivated_at: string|null,
 *     is_active: bool
 * }>
 */
final class ActivationDomain implements Response
{
	use InteractsWithDateTime;

	public DateTimeImmutable $activatedAt;

	public ?DateTimeImmutable $deactivatedAt;

	public bool $isActive;

	private function __construct(
		DateTimeImmutable $activatedAt,
		?DateTimeImmutable $deactivatedAt,
		bool $isActive
	) {
		$this->activatedAt   = $activatedAt;
		$this->deactivatedAt = $deactivatedAt;
		$this->isActive      = $isActive;
	}

	/**
	 * @param array{
	 *     activated_at: string,
	 *     deactivated_at: string|null,
	 *     is_active: bool
	 * } $attributes
	 *
	 * @throws UnexpectedResponseException
	 */
	public static function from(array $attributes): self {
		return new self(
			self::parseDateTime($attributes['activated_at']),
			$attributes['deactivated_at'] !== null
				? self::parseDateTime($attributes['deactivated_at'])
				: null,
			$attributes['is_active']
		);
	}

	public function toArray(): array {
		return [
			'activated_at'   => $this->formatDateTime($this->activatedAt),
			'deactivated_at' => $this->deactivatedAt ? $this->formatDateTime($this->deactivatedAt) : null,
			'is_active'      => $this->isActive,
		];
	}
}
