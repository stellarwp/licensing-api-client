<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use Generator;
use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Requests\Credit\ListLedgerEntries;
use LiquidWeb\LicensingApiClient\Responses\Credit\LedgerPage;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Defines the credits ledger resource surface.
 */
interface CreditsLedgerResourceInterface
{
	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function list(ListLedgerEntries $request): LedgerPage;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Generator<int, LedgerPage, mixed, void>
	 */
	public function pages(ListLedgerEntries $request): Generator;
}
