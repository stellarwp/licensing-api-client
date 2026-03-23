<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use LiquidWeb\LicensingApiClient\Requests\Entitlement\Upsert;
use LiquidWeb\LicensingApiClient\Responses\Entitlement\Cancel;
use LiquidWeb\LicensingApiClient\Responses\Entitlement\Delete;
use LiquidWeb\LicensingApiClient\Responses\Entitlement\Suspend;
use LiquidWeb\LicensingApiClient\Responses\Entitlement\Unsuspend;
use LiquidWeb\LicensingApiClient\Responses\Entitlement\Upsert as UpsertResponse;
use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;

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
