<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient;

use LiquidWeb\LicensingApiClient\Contracts\LicensingClientInterface;
use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Http\RequestHeaderCollection;
use LiquidWeb\LicensingApiClient\Resources\Contracts\CreditsResourceInterface;
use LiquidWeb\LicensingApiClient\Resources\Contracts\EntitlementsResourceInterface;
use LiquidWeb\LicensingApiClient\Resources\Contracts\LicensesResourceInterface;
use LiquidWeb\LicensingApiClient\Resources\Contracts\ProductsResourceInterface;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsResource;
use LiquidWeb\LicensingApiClient\Resources\EntitlementsResource;
use LiquidWeb\LicensingApiClient\Resources\LicensesResource;
use LiquidWeb\LicensingApiClient\Resources\ProductsResource;

/**
 * Exposes the built API resources and immutable auth-state transitions.
 */
final class Api implements LicensingClientInterface
{
	private AuthState $authState;

	private RequestHeaderCollection $requestHeaderCollection;

	private LicensesResource $licenses;

	private ProductsResource $products;

	private CreditsResource $credits;

	private EntitlementsResource $entitlements;

	public function __construct(
		AuthState $authState,
		RequestHeaderCollection $requestHeaderCollection,
		LicensesResource $licenses,
		ProductsResource $products,
		CreditsResource $credits,
		EntitlementsResource $entitlements
	) {
		$this->authState               = $authState;
		$this->requestHeaderCollection = $requestHeaderCollection;
		$this->licenses                = $licenses;
		$this->products                = $products;
		$this->credits                 = $credits;
		$this->entitlements            = $entitlements;
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
			$this->requestHeaderCollection,
			$this->licenses->withAuthState($authState),
			$this->products->withAuthState($authState),
			$this->credits->withAuthState($authState),
			$this->entitlements->withAuthState($authState)
		);
	}

	/**
	 * @param array<string, string|int|float|bool> $headers
	 */
	public function withHeaders(array $headers): LicensingClientInterface {
		return $this->cloneWithRequestHeaderCollection($this->requestHeaderCollection->withHeaders($headers));
	}

	public function withTraceId(string $traceId): LicensingClientInterface {
		return $this->cloneWithRequestHeaderCollection($this->requestHeaderCollection->withTraceId($traceId));
	}

	public function withoutHeaders(): LicensingClientInterface {
		return $this->cloneWithRequestHeaderCollection($this->requestHeaderCollection->withoutHeaders());
	}

	private function cloneWithRequestHeaderCollection(RequestHeaderCollection $requestHeaderCollection): self {
		if ($this->requestHeaderCollection === $requestHeaderCollection) {
			return $this;
		}

		return new self(
			$this->authState,
			$requestHeaderCollection,
			$this->licenses->withRequestHeaderCollection($requestHeaderCollection),
			$this->products->withRequestHeaderCollection($requestHeaderCollection),
			$this->credits->withRequestHeaderCollection($requestHeaderCollection),
			$this->entitlements->withRequestHeaderCollection($requestHeaderCollection)
		);
	}

	public function withConfiguredToken(): LicensingClientInterface {
		return $this->cloneWithAuthState($this->authState->withConfiguredToken());
	}

	public function withToken(string $token): LicensingClientInterface {
		return $this->cloneWithAuthState($this->authState->withToken($token));
	}
}
