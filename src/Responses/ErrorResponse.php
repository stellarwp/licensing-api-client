<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses;

use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;

/**
 * Represents a standard API business error response.
 *
 * @implements Response<array{code: string, message: string}>
 */
final class ErrorResponse implements Response
{
	public string $code;

	public string $message;

	private function __construct(string $code, string $message) {
		$this->code    = $code;
		$this->message = $message;
	}

	/**
	 * @param array{code: string, message: string} $attributes
	 */
	public static function from(array $attributes): self {
		return new self($attributes['code'], $attributes['message']);
	}

	public function toArray(): array {
		return [
			'code'    => $this->code,
			'message' => $this->message,
		];
	}
}
