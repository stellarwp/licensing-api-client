<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Credit;

use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Http\Factories\ApiUriFactory;
use LiquidWeb\LicensingApiClient\Http\RequestExecutor;
use LiquidWeb\LicensingApiClient\Requests\Credit\CreatePool;
use LiquidWeb\LicensingApiClient\Requests\Credit\DeletePool as DeletePoolRequest;
use LiquidWeb\LicensingApiClient\Requests\Credit\UpdatePool;
use LiquidWeb\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use LiquidWeb\LicensingApiClient\Resources\Contracts\CreditsPoolsResourceInterface;
use LiquidWeb\LicensingApiClient\Responses\Credit\DeletePool;
use LiquidWeb\LicensingApiClient\Responses\Credit\PoolCollection;
use LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects\CreditPool;
use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Provides operations for the credits pools API resource.
 *
 * @phpstan-import-type CreatePoolPayload from CreatePool
 * @phpstan-import-type UpdatePoolPayload from UpdatePool
 * @phpstan-import-type DeletePoolPayload from DeletePoolRequest
 * @phpstan-type PoolPayload array{
 *     pool_id: int,
 *     credit_type: string,
 *     credits_total: int,
 *     credits_used: int,
 *     overage_limit: ?int,
 *     priority: int,
 *     period: string,
 *     first_period_start: ?string,
 *     expires_at: ?string,
 *     is_expired: bool
 * }
 * @phpstan-type PoolCollectionPayload array{
 *     pools: array<int|string, PoolPayload>
 * }
 * @phpstan-type DeletePoolResponsePayload array{
 *     deleted: bool,
 *     pool_id: int
 * }
 */
final class CreditsPoolsResource implements CreditsPoolsResourceInterface
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
	 * @return PoolCollection|ErrorResponse
	 */
	public function list(string $key, bool $active = false) {
		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->apiUriFactory->make('/credits/pools'),
			[
				'key'    => $key,
				'active' => $active,
			],
			null,
			$this->authState->requiredToken()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var PoolCollectionPayload $result */
		return PoolCollection::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return CreditPool|ErrorResponse
	 */
	public function create(CreatePool $request) {
		/** @var CreatePoolPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/credits/pools'),
			[],
			$body,
			$this->authState->requiredToken()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var PoolPayload $result */
		return CreditPool::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return CreditPool|ErrorResponse
	 */
	public function update(UpdatePool $request) {
		/** @var UpdatePoolPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'PATCH',
			$this->apiUriFactory->make('/credits/pools'),
			[],
			$body,
			$this->authState->requiredToken()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var PoolPayload $result */
		return CreditPool::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return DeletePool|ErrorResponse
	 */
	public function delete(DeletePoolRequest $request) {
		/** @var DeletePoolPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'DELETE',
			$this->apiUriFactory->make('/credits/pools'),
			[],
			$body,
			$this->authState->requiredToken()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var DeletePoolResponsePayload $result */
		return DeletePool::from($result);
	}
}
