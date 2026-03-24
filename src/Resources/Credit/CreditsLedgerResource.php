<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Credit;

use Generator;
use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Http\Factories\ApiUriFactory;
use LiquidWeb\LicensingApiClient\Http\RequestExecutor;
use LiquidWeb\LicensingApiClient\Requests\Credit\ListLedgerEntries;
use LiquidWeb\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use LiquidWeb\LicensingApiClient\Resources\Contracts\CreditsLedgerResourceInterface;
use LiquidWeb\LicensingApiClient\Responses\Credit\LedgerPage;
use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;
use Psr\Http\Client\ClientExceptionInterface;

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
 *     links: array{
 *         first: string,
 *         last: string|null,
 *         prev: string|null,
 *         next: string|null
 *     },
 *     meta: array{
 *         page: array{
 *             total: int,
 *             limit: int,
 *             max_size: int
 *         }
 *     }
 * }
 */
final class CreditsLedgerResource implements CreditsLedgerResourceInterface
{
	use RebindsAuthState;

	private RequestExecutor $requestExecutor;

	private ApiUriFactory $apiUriFactory;

	private AuthState $authState;

	public function __construct(
		RequestExecutor $requestExecutor,
		ApiUriFactory $apiUriFactory,
		AuthState $authState
	) {
		$this->requestExecutor = $requestExecutor;
		$this->apiUriFactory   = $apiUriFactory;
		$this->authState       = $authState;
	}

	protected function rebindWithAuthState(AuthState $authState): self {
		return new self($this->requestExecutor, $this->apiUriFactory, $authState);
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
			$this->apiUriFactory->make('/credits/ledger'),
			$query,
			null,
			$this->authState->requiredToken()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var LedgerPagePayload $result */
		return LedgerPage::from($result);
	}

	/**
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Generator<int, LedgerPage|ErrorResponse, mixed, void>
	 */
	public function pages(ListLedgerEntries $request): Generator {
		$page = $this->list($request);

		while (true) {
			yield $page;

			if ($page instanceof ErrorResponse || $page->links->next === null) {
				return;
			}

			$result = $this->requestExecutor->executeJson(
				'GET',
				$this->apiUriFactory->fromPaginationLink($page->links->next),
				[],
				null,
				$this->authState->requiredToken()
			);

			if ($result instanceof ErrorResponse) {
				yield $result;

				return;
			}

			/** @var LedgerPagePayload $result */
			$page = LedgerPage::from($result);
		}
	}
}
