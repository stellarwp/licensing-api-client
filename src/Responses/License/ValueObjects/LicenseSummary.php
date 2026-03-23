<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Responses\License\ValueObjects;

use StellarWP\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents the top-level license details in a validation response.
 *
 * @implements Response<array{key: string, status: string}>
 */
final class LicenseSummary implements Response
{
	public string $key;

	public string $status;

	private function __construct(string $key, string $status) {
		$this->key    = $key;
		$this->status = $status;
	}

	/**
	 * @param array{key: string, status: string} $attributes
	 */
	public static function from(array $attributes): self {
		return new self($attributes['key'], $attributes['status']);
	}

	public function toArray(): array {
		return [
			'key'    => $this->key,
			'status' => $this->status,
		];
	}
}
