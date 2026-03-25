<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Credit;

use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\ApiResponseException;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Http\Factories\ApiUriFactory;
use LiquidWeb\LicensingApiClient\Http\RequestExecutor;
use LiquidWeb\LicensingApiClient\Requests\Credit\SetQuota;
use LiquidWeb\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use LiquidWeb\LicensingApiClient\Resources\Contracts\CreditsQuotasResourceInterface;
use LiquidWeb\LicensingApiClient\Responses\Credit\DeleteQuota;
use LiquidWeb\LicensingApiClient\Responses\Credit\QuotaCollection;
use LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects\SiteQuota;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Provides operations for the credits quotas API resource.
 *
 * @phpstan-import-type SetQuotaPayload from \LiquidWeb\LicensingApiClient\Requests\Credit\SetQuota
 * @phpstan-type SiteQuotaPayload array{
 *     domain: string,
 *     credit_type: string,
 *     quota: ?int,
 *     period: string,
 *     first_period_start: ?string,
 *     is_blocked: bool,
 *     is_uncapped: bool
 * }
 * @phpstan-type QuotaCollectionPayload array{
 *     quotas: list<SiteQuotaPayload>
 * }
 * @phpstan-type DeleteQuotaPayload array{
 *     deleted: bool
 * }
 */
final class CreditsQuotasResource implements CreditsQuotasResourceInterface
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

	/**
	 * @throws ApiResponseException
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function list(string $key): QuotaCollection {
		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->apiUriFactory->make('/credits/quotas'),
			[
				'key' => $key,
			],
			null,
			$this->authState->requiredToken()
		);

		/** @var QuotaCollectionPayload $result */
		return QuotaCollection::from($result);
	}

	/**
	 * @throws ApiResponseException
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function set(SetQuota $request): SiteQuota {
		/** @var SetQuotaPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/credits/quotas'),
			[],
			$body,
			$this->authState->requiredToken()
		);

		/** @var SiteQuotaPayload $result */
		return SiteQuota::from($result);
	}

	/**
	 * @throws ApiResponseException
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function delete(string $key, string $domain, string $creditType): DeleteQuota {
		$result = $this->requestExecutor->executeJson(
			'DELETE',
			$this->apiUriFactory->make('/credits/quotas'),
			[],
			[
				'key'         => $key,
				'domain'      => $domain,
				'credit_type' => $creditType,
			],
			$this->authState->requiredToken()
		);

		/** @var DeleteQuotaPayload $result */
		return DeleteQuota::from($result);
	}

	protected function rebindWithAuthState(AuthState $authState): self {
		return new self($this->requestExecutor, $this->apiUriFactory, $authState);
	}
}
