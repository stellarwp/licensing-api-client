<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources\Contracts;

use StellarWP\LicensingApiClient\Requests\Credit\RecordUsage as RecordUsageRequest;
use StellarWP\LicensingApiClient\Requests\Credit\Refund as RefundRequest;
use StellarWP\LicensingApiClient\Responses\Credit\BalanceCollection;
use StellarWP\LicensingApiClient\Responses\Credit\RecordUsage;
use StellarWP\LicensingApiClient\Responses\Credit\Refund;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;

/**
 * Defines the root credits resource surface.
 */
interface CreditsResourceInterface
{
	/**
	 * @return BalanceCollection|ErrorResponse
	 */
	public function balance(string $key, string $domain, ?string $creditType = null, ?string $sort = null);

	/**
	 * @return RecordUsage|ErrorResponse
	 */
	public function recordUsage(RecordUsageRequest $request);

	/**
	 * @return Refund|ErrorResponse
	 */
	public function refund(RefundRequest $request);

	public function pools(): CreditsPoolsResourceInterface;

	public function quotas(): CreditsQuotasResourceInterface;

	public function ledger(): CreditsLedgerResourceInterface;
}
