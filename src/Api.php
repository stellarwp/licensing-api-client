<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient;

use StellarWP\LicensingApiClient\Contracts\LicensingClientInterface;
use StellarWP\LicensingApiClient\Http\AuthState;
use StellarWP\LicensingApiClient\Resources\Contracts\CreditsResourceInterface;
use StellarWP\LicensingApiClient\Resources\Contracts\EntitlementsResourceInterface;
use StellarWP\LicensingApiClient\Resources\Contracts\LicensesResourceInterface;
use StellarWP\LicensingApiClient\Resources\Contracts\ProductsResourceInterface;
use StellarWP\LicensingApiClient\Resources\Credit\CreditsResource;
use StellarWP\LicensingApiClient\Resources\EntitlementsResource;
use StellarWP\LicensingApiClient\Resources\LicensesResource;
use StellarWP\LicensingApiClient\Resources\ProductsResource;

/**
 * Exposes the built API resources and immutable auth-state transitions.
 */
final class Api implements LicensingClientInterface
{
	private AuthState $authState;

	private LicensesResource $licenses;

	private ProductsResource $products;

	private CreditsResource $credits;

	private EntitlementsResource $entitlements;

	public function __construct(
		AuthState $authState,
		LicensesResource $licenses,
		ProductsResource $products,
		CreditsResource $credits,
		EntitlementsResource $entitlements
	) {
		$this->authState    = $authState;
		$this->licenses     = $licenses;
		$this->products     = $products;
		$this->credits      = $credits;
		$this->entitlements = $entitlements;
	}

	public function entitlements(): EntitlementsResourceInterface {
		return $this->entitlements;
	}

	public function licenses(): LicensesResourceInterface {
		return $this->licenses;
	}

	public function products(): ProductsResourceInterface {
		return $this->products;
	}

	public function credits(): CreditsResourceInterface {
		return $this->credits;
	}

	public function withoutAuth(): LicensingClientInterface {
		return $this->cloneWithAuthState($this->authState->withoutAuth());
	}

	private function cloneWithAuthState(AuthState $authState): self {
		return new self(
			$authState,
			$this->licenses->withAuthState($authState),
			$this->products->withAuthState($authState),
			$this->credits->withAuthState($authState),
			$this->entitlements->withAuthState($authState)
		);
	}

	public function withConfiguredToken(): LicensingClientInterface {
		return $this->cloneWithAuthState($this->authState->withConfiguredToken());
	}

	public function withToken(string $token): LicensingClientInterface {
		return $this->cloneWithAuthState($this->authState->withExplicitToken($token));
	}
}
