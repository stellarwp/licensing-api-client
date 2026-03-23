<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources\Contracts;

use StellarWP\LicensingApiClient\Requests\Credit\SetQuota;
use StellarWP\LicensingApiClient\Responses\Credit\DeleteQuota;
use StellarWP\LicensingApiClient\Responses\Credit\QuotaCollection;
use StellarWP\LicensingApiClient\Responses\Credit\ValueObjects\SiteQuota;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;

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
