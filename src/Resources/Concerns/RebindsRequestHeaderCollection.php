<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Concerns;

use LiquidWeb\LicensingApiClient\Http\RequestHeaderCollection;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsLedgerResource;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsPoolsResource;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsQuotasResource;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsResource;
use LiquidWeb\LicensingApiClient\Resources\EntitlementsResource;
use LiquidWeb\LicensingApiClient\Resources\LicensesResource;
use LiquidWeb\LicensingApiClient\Resources\ProductsResource;
use LiquidWeb\LicensingApiClient\Resources\TokensResource;

/**
 * Provides immutable request-header rebinding for resource views.
 *
 * @mixin CreditsLedgerResource
 * @mixin CreditsPoolsResource
 * @mixin CreditsQuotasResource
 * @mixin CreditsResource
 * @mixin EntitlementsResource
 * @mixin LicensesResource
 * @mixin ProductsResource
 * @mixin TokensResource
 */
trait RebindsRequestHeaderCollection
{
	public function withRequestHeaderCollection(RequestHeaderCollection $requestHeaderCollection): self {
		if ($this->requestHeaderCollection === $requestHeaderCollection) {
			return $this;
		}

		return $this->rebindWithRequestHeaderCollection($requestHeaderCollection);
	}

	abstract protected function rebindWithRequestHeaderCollection(RequestHeaderCollection $requestHeaderCollection): self;
}
