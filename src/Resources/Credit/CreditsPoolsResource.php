<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources\Credit;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use StellarWP\LicensingApiClient\Exceptions\MissingAuthenticationException;
use StellarWP\LicensingApiClient\Exceptions\UnexpectedResponseException;
use StellarWP\LicensingApiClient\Http\AuthState;
use StellarWP\LicensingApiClient\Http\Factories\EndpointFactory;
use StellarWP\LicensingApiClient\Http\RequestExecutor;
use StellarWP\LicensingApiClient\Requests\Credit\CreatePool;
use StellarWP\LicensingApiClient\Requests\Credit\DeletePool as DeletePoolRequest;
use StellarWP\LicensingApiClient\Requests\Credit\UpdatePool;
use StellarWP\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use StellarWP\LicensingApiClient\Resources\Contracts\CreditsPoolsResourceInterface;
use StellarWP\LicensingApiClient\Responses\Credit\DeletePool;
use StellarWP\LicensingApiClient\Responses\Credit\PoolCollection;
use StellarWP\LicensingApiClient\Responses\Credit\ValueObjects\CreditPool;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;

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
	 * @return PoolCollection|ErrorResponse
	 */
	public function list(string $key, bool $active = false) {
		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->endpointFactory->make('/credits/pools'),
			[
				'key'    => $key,
				'active' => $active,
			],
			null,
			$this->authState->resolveRequiredTokenOrFail()
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
			$this->endpointFactory->make('/credits/pools'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
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
			$this->endpointFactory->make('/credits/pools'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
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
			$this->endpointFactory->make('/credits/pools'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var DeletePoolResponsePayload $result */
		return DeletePool::from($result);
	}
}
