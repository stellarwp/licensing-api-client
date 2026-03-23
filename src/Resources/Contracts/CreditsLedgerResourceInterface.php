<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use Generator;
use LiquidWeb\LicensingApiClient\Requests\Credit\ListLedgerEntries;
use LiquidWeb\LicensingApiClient\Responses\Credit\LedgerPage;
use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;

/**
 * Defines the credits ledger resource surface.
 */
interface CreditsLedgerResourceInterface
{
	/**
	 * @return LedgerPage|ErrorResponse
	 */
	public function list(ListLedgerEntries $request);

	/**
	 * @return Generator<int, LedgerPage|ErrorResponse, mixed, void>
	 */
	public function pages(ListLedgerEntries $request): Generator;
}
