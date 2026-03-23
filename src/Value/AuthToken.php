<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Value;

use InvalidArgumentException;

/**
 * Represents a normalized non-empty authentication token value.
 */
final class AuthToken
{
	private string $value;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $value) {
		$value = trim($value);

		if ($value === '') {
			throw new InvalidArgumentException('Authentication token cannot be empty.');
		}

		$this->value = $value;
	}

	public function equals(self $authToken): bool {
		return $this->value === $authToken->value();
	}

	public function value(): string {
		return $this->value;
	}
}
