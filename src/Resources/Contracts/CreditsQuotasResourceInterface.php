<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use LiquidWeb\LicensingApiClient\Requests\Credit\SetQuota;
use LiquidWeb\LicensingApiClient\Responses\Credit\DeleteQuota;
use LiquidWeb\LicensingApiClient\Responses\Credit\QuotaCollection;
use LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects\SiteQuota;
use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;

/**
 * Defines the credits quotas resource surface.
 */
interface CreditsQuotasResourceInterface
{
	/**
	 * @return QuotaCollection|ErrorResponse
	 */
	public function list(string $key);

	/**
	 * @return SiteQuota|ErrorResponse
	 */
	public function set(SetQuota $request);

	/**
	 * @return DeleteQuota|ErrorResponse
	 */
	public function delete(string $key, string $domain, string $creditType);
}
