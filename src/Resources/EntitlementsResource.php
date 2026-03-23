<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources;

use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Http\Factories\ApiUriFactory;
use LiquidWeb\LicensingApiClient\Http\RequestBuilder;
use LiquidWeb\LicensingApiClient\Http\RequestExecutor;
use LiquidWeb\LicensingApiClient\Requests\Entitlement\Upsert as UpsertRequest;
use LiquidWeb\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use LiquidWeb\LicensingApiClient\Resources\Contracts\EntitlementsResourceInterface;
use LiquidWeb\LicensingApiClient\Responses\Entitlement\Cancel;
use LiquidWeb\LicensingApiClient\Responses\Entitlement\Delete;
use LiquidWeb\LicensingApiClient\Responses\Entitlement\Suspend;
use LiquidWeb\LicensingApiClient\Responses\Entitlement\Unsuspend;
use LiquidWeb\LicensingApiClient\Responses\Entitlement\Upsert as UpsertResponse;
use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Provides operations for the entitlements API resource.
 *
 * @phpstan-import-type JsonObject from RequestBuilder
 * @phpstan-type EntitlementStatusPayload array{
 *     product_slug: string,
 *     tier: string,
 *     status: string
 * }
 * @phpstan-type UpsertPayload array{
 *     license_key: string,
 *     products: list<array{
 *         product_slug: string,
 *         tier: string,
 *         status: string
 *     }>
 * }
 * @phpstan-type DeletePayload array{
 *     deleted: bool
 * }
 */
final class EntitlementsResource implements EntitlementsResourceInterface
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
	 * @return UpsertResponse|ErrorResponse
	 */
	public function upsert(UpsertRequest $request) {
		/** @var JsonObject $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/entitlements'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var UpsertPayload $result */
		return UpsertResponse::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Suspend|ErrorResponse
	 */
	public function suspend(string $key, string $productSlug, string $tier) {
		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/entitlements/suspend'),
			[],
			[
				'key'          => $key,
				'product_slug' => $productSlug,
				'tier'         => $tier,
			],
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var EntitlementStatusPayload $result */
		return Suspend::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Unsuspend|ErrorResponse
	 */
	public function unsuspend(string $key, string $productSlug, string $tier) {
		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/entitlements/unsuspend'),
			[],
			[
				'key'          => $key,
				'product_slug' => $productSlug,
				'tier'         => $tier,
			],
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var EntitlementStatusPayload $result */
		return Unsuspend::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Cancel|ErrorResponse
	 */
	public function cancel(string $key, string $productSlug, string $tier, ?string $reason = null) {
		$body = array_filter([
			'key'          => $key,
			'product_slug' => $productSlug,
			'tier'         => $tier,
			'reason'       => $reason,
		], static fn ($value): bool => $value !== null);

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/entitlements/cancel'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var EntitlementStatusPayload $result */
		return Cancel::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Delete|ErrorResponse
	 */
	public function delete(string $key, string $productSlug, string $tier) {
		$result = $this->requestExecutor->executeJson(
			'DELETE',
			$this->apiUriFactory->make('/entitlements'),
			[],
			[
				'key'          => $key,
				'product_slug' => $productSlug,
				'tier'         => $tier,
			],
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var DeletePayload $result */
		return Delete::from($result);
	}
}
