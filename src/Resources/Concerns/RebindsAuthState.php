<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Concerns;

use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsLedgerResource;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsPoolsResource;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsQuotasResource;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsResource;
use LiquidWeb\LicensingApiClient\Resources\EntitlementsResource;
use LiquidWeb\LicensingApiClient\Resources\LicensesResource;
use LiquidWeb\LicensingApiClient\Resources\ProductsResource;

/**
 * Provides immutable auth-state rebinding for auth-bound resource views.
 *
 * @mixin CreditsLedgerResource
 * @mixin CreditsPoolsResource
 * @mixin CreditsQuotasResource
 * @mixin CreditsResource
 * @mixin EntitlementsResource
 * @mixin LicensesResource
 * @mixin ProductsResource
 */
trait RebindsAuthState
{
	/**
	 * Returns the current resource when the auth state is unchanged, or a rebound
	 * resource view when a different auth state is requested.
	 */
	public function withAuthState(AuthState $authState): self {
		if ($this->authState === $authState) {
			return $this;
		}

		return $this->rebindWithAuthState($authState);
	}

	/**
	 * Rebuilds the concrete resource with the provided auth state.
	 */
	abstract protected function rebindWithAuthState(AuthState $authState): self;
}
