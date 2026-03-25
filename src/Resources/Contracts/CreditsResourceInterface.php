<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Requests\Credit\RecordUsage as RecordUsageRequest;
use LiquidWeb\LicensingApiClient\Requests\Credit\Refund as RefundRequest;
use LiquidWeb\LicensingApiClient\Responses\Credit\BalanceCollection;
use LiquidWeb\LicensingApiClient\Responses\Credit\RecordUsage;
use LiquidWeb\LicensingApiClient\Responses\Credit\Refund;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Defines the root credits resource surface.
 */
interface CreditsResourceInterface
{
	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function balance(string $key, string $domain, ?string $creditType = null, ?string $sort = null): BalanceCollection;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function recordUsage(RecordUsageRequest $request): RecordUsage;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function refund(RefundRequest $request): Refund;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function pools(): CreditsPoolsResourceInterface;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function quotas(): CreditsQuotasResourceInterface;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function ledger(): CreditsLedgerResourceInterface;
}
