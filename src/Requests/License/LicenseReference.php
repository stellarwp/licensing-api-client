<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Requests\License;

use InvalidArgumentException;

/**
 * Represents a license reference identified by key or identity ID.
 *
 * @phpstan-type LicenseReferencePayload array{
 *     key?: string,
 *     identity_id?: string
 * }
 */
final class LicenseReference
{
	/**
	 * License key identifier.
	 *
	 * @example LWSW-8H9F-5UKA-VR3B-D7SQ-BP9N
	 */
	public ?string $key;

	/**
	 * Customer identity identifier.
	 *
	 * @example identity_123
	 */
	public ?string $identityId;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(?string $key = null, ?string $identityId = null) {
		if ($key === null && $identityId === null) {
			throw new InvalidArgumentException('Either key or identityId is required.');
		}

		$this->key        = $key;
		$this->identityId = $identityId;
	}

	/**
	 * @return LicenseReferencePayload
	 */
	public function toArray(): array {
		return array_filter([
			'key'         => $this->key,
			'identity_id' => $this->identityId,
		], static fn ($value): bool => $value !== null);
	}
}
