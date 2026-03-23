<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources\Contracts;

use Generator;
use StellarWP\LicensingApiClient\Requests\Credit\ListLedgerEntries;
use StellarWP\LicensingApiClient\Responses\Credit\LedgerPage;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;

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
