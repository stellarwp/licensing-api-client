<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources\Credit;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use StellarWP\LicensingApiClient\Exceptions\MissingAuthenticationException;
use StellarWP\LicensingApiClient\Exceptions\UnexpectedResponseException;
use StellarWP\LicensingApiClient\Http\AuthState;
use StellarWP\LicensingApiClient\Http\Factories\EndpointFactory;
use StellarWP\LicensingApiClient\Http\RequestExecutor;
use StellarWP\LicensingApiClient\Requests\Credit\ListLedgerEntries;
use StellarWP\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use StellarWP\LicensingApiClient\Resources\Contracts\CreditsLedgerResourceInterface;
use StellarWP\LicensingApiClient\Responses\Credit\LedgerPage;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;

/**
 * Provides operations for the credits ledger API resource.
 *
 * @phpstan-import-type ListLedgerEntriesQuery from ListLedgerEntries
 * @phpstan-type LedgerEntryPayload array{
 *     id: int,
 *     pool_id: int,
 *     activation_id: int,
 *     domain: string,
 *     product_slug: string,
 *     credit_type: string,
 *     entry_type: string,
 *     amount: int,
 *     period_start: string|null,
 *     idempotency_key: string,
 *     created_at: string
 * }
 * @phpstan-type LedgerPagePayload array{
 *     entries: list<LedgerEntryPayload>,
 *     limit: int,
 *     next_cursor: int|null
 * }
 */
final class CreditsLedgerResource implements CreditsLedgerResourceInterface
{
	use RebindsAuthState;

	private RequestExecutor $requestExecutor;

	private EndpointFactory $endpointFactory;

	private AuthState $authState;

	public function __construct(
		RequestExecutor $requestExecutor,
		EndpointFactory $endpointFactory,
		AuthState $authState
	) {
		$this->requestExecutor = $requestExecutor;
		$this->endpointFactory = $endpointFactory;
		$this->authState       = $authState;
	}

	protected function rebindWithAuthState(AuthState $authState): self {
		return new self($this->requestExecutor, $this->endpointFactory, $authState);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return LedgerPage|ErrorResponse
	 */
	public function list(ListLedgerEntries $request) {
		/** @var ListLedgerEntriesQuery $query */
		$query = $request->toQuery();

		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->endpointFactory->make('/credits/ledger'),
			$query,
			null,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var LedgerPagePayload $result */
		return LedgerPage::from($result);
	}
}
