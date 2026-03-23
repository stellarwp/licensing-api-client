<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Http;

use InvalidArgumentException;
use Stringable;

/**
 * Represents an API version segment used when building endpoint URLs.
 */
final class ApiVersion implements Stringable
{
	public const V4 = 'v4';

	private string $value;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $value)
	{
		$value = trim($value, '/');

		if ($value === '') {
			throw new InvalidArgumentException('API version cannot be empty.');
		}

		$this->value = $value;
	}

	public static function default(): self
	{
		return new self(self::V4);
	}

	public function value(): string
	{
		return $this->value;
	}

	public function __toString(): string
	{
		return $this->value;
	}
}
