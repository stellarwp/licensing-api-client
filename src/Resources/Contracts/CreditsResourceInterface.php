<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use LiquidWeb\LicensingApiClient\Requests\Credit\RecordUsage as RecordUsageRequest;
use LiquidWeb\LicensingApiClient\Requests\Credit\Refund as RefundRequest;
use LiquidWeb\LicensingApiClient\Responses\Credit\BalanceCollection;
use LiquidWeb\LicensingApiClient\Responses\Credit\RecordUsage;
use LiquidWeb\LicensingApiClient\Responses\Credit\Refund;
use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;

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
