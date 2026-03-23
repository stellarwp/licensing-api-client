<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Credit;

use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Http\Factories\ApiUriFactory;
use LiquidWeb\LicensingApiClient\Http\RequestExecutor;
use LiquidWeb\LicensingApiClient\Requests\Credit\RecordUsage as RecordUsageRequest;
use LiquidWeb\LicensingApiClient\Requests\Credit\Refund as RefundRequest;
use LiquidWeb\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use LiquidWeb\LicensingApiClient\Resources\Contracts\CreditsLedgerResourceInterface;
use LiquidWeb\LicensingApiClient\Resources\Contracts\CreditsPoolsResourceInterface;
use LiquidWeb\LicensingApiClient\Resources\Contracts\CreditsQuotasResourceInterface;
use LiquidWeb\LicensingApiClient\Resources\Contracts\CreditsResourceInterface;
use LiquidWeb\LicensingApiClient\Responses\Credit\BalanceCollection;
use LiquidWeb\LicensingApiClient\Responses\Credit\RecordUsage;
use LiquidWeb\LicensingApiClient\Responses\Credit\Refund;
use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Provides operations for the credits API resource.
 *
 * @phpstan-type BalancePayload array{
 *     credits: list<array{
 *         credit_type: string,
 *         remaining: int,
 *         site_quota: int|null,
 *         site_used: int,
 *         site_remaining: int,
 *         aggregate_total: int,
 *         aggregate_used: int,
 *         aggregate_remaining: int,
 *         aggregate_overage: int,
 *         pools: list<array{
 *             pool_id: int,
 *             pool_remaining: int,
 *             priority: int,
 *             period: string,
 *             resets_on: string|null,
 *             expires_at: string|null,
 *             credits_total?: int,
 *             credits_used?: int,
 *             overage?: int,
 *             overage_limit?: int|null
 *         }>
 *     }>
 * }
 * @phpstan-import-type RecordUsagePayload from RecordUsageRequest
 * @phpstan-import-type RefundPayload from RefundRequest
 * @phpstan-type RecordUsageResponsePayload array{
 *     credits_used: int,
 *     pool_remaining: int,
 *     site_remaining: int|null,
 *     pool_breakdown: array<array-key, int>
 * }
 * @phpstan-type RefundResponsePayload array{
 *     credits_refunded: int,
 *     pool_remaining: int,
 *     site_remaining: int|null,
 *     pool_breakdown: array<array-key, int>
 * }
 */
final class CreditsResource implements CreditsResourceInterface
{
	use RebindsAuthState;

	private RequestExecutor $requestExecutor;

	private ApiUriFactory $apiUriFactory;

	private AuthState $authState;

	private CreditsPoolsResource $pools;

	private CreditsQuotasResource $quotas;

	private CreditsLedgerResource $ledger;

	public function __construct(
		RequestExecutor $requestExecutor,
		ApiUriFactory $apiUriFactory,
		AuthState $authState,
		CreditsPoolsResource $pools,
		CreditsQuotasResource $quotas,
		CreditsLedgerResource $ledger
	) {
		$this->requestExecutor = $requestExecutor;
		$this->apiUriFactory   = $apiUriFactory;
		$this->authState       = $authState;
		$this->pools           = $pools;
		$this->quotas          = $quotas;
		$this->ledger          = $ledger;
	}

	protected function rebindWithAuthState(AuthState $authState): self {
		return new self(
			$this->requestExecutor,
			$this->apiUriFactory,
			$authState,
			$this->pools->withAuthState($authState),
			$this->quotas->withAuthState($authState),
			$this->ledger->withAuthState($authState)
		);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return BalanceCollection|ErrorResponse
	 */
	public function balance(string $key, string $domain, ?string $creditType = null, ?string $sort = null) {
		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->apiUriFactory->make('/credits'),
			array_filter([
				'key'         => $key,
				'domain'      => $domain,
				'credit_type' => $creditType,
				'sort'        => $sort,
			], static fn ($value): bool => $value !== null),
			null,
			$this->authState->resolveTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var BalancePayload $result */
		return BalanceCollection::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return RecordUsage|ErrorResponse
	 */
	public function recordUsage(RecordUsageRequest $request) {
		/** @var RecordUsagePayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/credits/usage'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail(),
			[
				'X-Idempotency-Key' => $request->idempotencyKey,
			]
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var RecordUsageResponsePayload $result */
		return RecordUsage::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Refund|ErrorResponse
	 */
	public function refund(RefundRequest $request) {
		/** @var RefundPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/credits/refunds'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail(),
			[
				'X-Idempotency-Key' => $request->idempotencyKey,
			]
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var RefundResponsePayload $result */
		return Refund::from($result);
	}

	public function pools(): CreditsPoolsResourceInterface {
		return $this->pools;
	}

	public function quotas(): CreditsQuotasResourceInterface {
		return $this->quotas;
	}

	public function ledger(): CreditsLedgerResourceInterface {
		return $this->ledger;
	}
}
