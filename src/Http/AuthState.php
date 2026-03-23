<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Http;

use StellarWP\LicensingApiClient\Exceptions\MissingAuthenticationException;
use StellarWP\LicensingApiClient\Value\AuthToken;

/**
 * Combines auth policy and configured token state for request execution.
 */
final class AuthState
{
	private AuthContext $authContext;

	private ?AuthToken $configuredToken;

	public function __construct(AuthContext $authContext, ?AuthToken $configuredToken = null) {
		$this->authContext     = $authContext;
		$this->configuredToken = $configuredToken;
	}

	/**
	 * @throws MissingAuthenticationException
	 */
	public function resolveTokenOrFail(): ?AuthToken {
		return $this->authContext->resolveTokenOrFail($this->configuredToken);
	}

	/**
	 * @throws MissingAuthenticationException
	 */
	public function resolveRequiredTokenOrFail(): AuthToken {
		$token = $this->resolveTokenOrFail();

		if ($token === null) {
			throw new MissingAuthenticationException(
				'This request requires authentication, but no token is available.'
			);
		}

		return $token;
	}

	public function withoutAuth(): self {
		return $this->withAuthContext(new AuthContext(AuthContext::MODE_NONE));
	}

	public function withConfiguredToken(): self {
		return $this->withAuthContext(new AuthContext(AuthContext::MODE_CONFIGURED));
	}

	public function withExplicitToken(string $token): self {
		return $this->withAuthContext(new AuthContext(AuthContext::MODE_EXPLICIT, $token));
	}

	public function authContext(): AuthContext {
		return $this->authContext;
	}

	public function configuredToken(): ?AuthToken {
		return $this->configuredToken;
	}

	private function withAuthContext(AuthContext $authContext): self {
		if ($this->authContext->equals($authContext)) {
			return $this;
		}

		return new self($authContext, $this->configuredToken);
	}
}
