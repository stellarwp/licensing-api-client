<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources\Contracts;

use StellarWP\LicensingApiClient\Requests\Entitlement\Upsert;
use StellarWP\LicensingApiClient\Responses\Entitlement\Cancel;
use StellarWP\LicensingApiClient\Responses\Entitlement\Delete;
use StellarWP\LicensingApiClient\Responses\Entitlement\Suspend;
use StellarWP\LicensingApiClient\Responses\Entitlement\Unsuspend;
use StellarWP\LicensingApiClient\Responses\Entitlement\Upsert as UpsertResponse;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;

/**
 * Defines the entitlements resource surface.
 */
interface EntitlementsResourceInterface
{
	/**
	 * @return UpsertResponse|ErrorResponse
	 */
	public function upsert(Upsert $request);

	/**
	 * @return Suspend|ErrorResponse
	 */
	public function suspend(string $key, string $productSlug, string $tier);

	/**
	 * @return Unsuspend|ErrorResponse
	 */
	public function unsuspend(string $key, string $productSlug, string $tier);

	/**
	 * @return Cancel|ErrorResponse
	 */
	public function cancel(string $key, string $productSlug, string $tier, ?string $reason = null);

	/**
	 * @return Delete|ErrorResponse
	 */
	public function delete(string $key, string $productSlug, string $tier);
}
