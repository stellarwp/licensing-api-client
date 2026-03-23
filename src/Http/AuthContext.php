<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Http;

use InvalidArgumentException;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Value\AuthToken;

/**
 * Represents the authentication mode the API client should use for a request.
 */
final class AuthContext
{
	public const MODE_AUTO       = 'auto';
	public const MODE_NONE       = 'none';
	public const MODE_CONFIGURED = 'configured';
	public const MODE_EXPLICIT   = 'explicit';

	private string $mode;

	private ?AuthToken $token;

	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $mode = self::MODE_AUTO, ?string $token = null) {
		$this->assertValidMode($mode);
		$token = $this->normalizeToken($token);

		if ($mode === self::MODE_EXPLICIT && $token === null) {
			throw new InvalidArgumentException('Explicit auth mode requires a token.');
		}

		$this->mode  = $mode;
		$this->token = $token;
	}

	/**
	 * @throws InvalidArgumentException
	 */
	private function assertValidMode(string $mode): void {
		$validModes = [
			self::MODE_AUTO,
			self::MODE_NONE,
			self::MODE_CONFIGURED,
			self::MODE_EXPLICIT,
		];

		if ( ! in_array($mode, $validModes, true)) {
			throw new InvalidArgumentException('Unsupported auth mode [' . $mode . '].');
		}
	}

	private function normalizeToken(?string $token): ?AuthToken {
		if ($token === null) {
			return null;
		}

		try {
			return new AuthToken($token);
		} catch (InvalidArgumentException $exception) {
			return null;
		}
	}

	/**
	 * @throws MissingAuthenticationException
	 */
	public function resolveTokenOrFail(?AuthToken $configuredToken): ?AuthToken {
		if ($this->mode === self::MODE_NONE) {
			return null;
		}

		if ($this->mode === self::MODE_EXPLICIT) {
			return $this->token;
		}

		if ($this->mode === self::MODE_CONFIGURED && $configuredToken === null) {
			throw new MissingAuthenticationException(
				'This request requires authentication, but no token is available.'
			);
		}

		return $configuredToken;
	}

	public function requiresToken(): bool {
		return $this->mode === self::MODE_CONFIGURED || $this->mode === self::MODE_EXPLICIT;
	}

	public function equals(self $authContext): bool {
		return $this->mode === $authContext->mode()
			&& (
				($this->token === null && $authContext->token() === null)
				|| ($this->token !== null && $authContext->token() !== null && $this->token->equals(
						$authContext->token()
					))
			);
	}

	public function mode(): string {
		return $this->mode;
	}

	public function token(): ?AuthToken {
		return $this->token;
	}
}
