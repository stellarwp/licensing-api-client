<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use StellarWP\LicensingApiClient\Exceptions\MissingAuthenticationException;
use StellarWP\LicensingApiClient\Exceptions\UnexpectedResponseException;
use StellarWP\LicensingApiClient\Http\AuthState;
use StellarWP\LicensingApiClient\Http\Factories\EndpointFactory;
use StellarWP\LicensingApiClient\Http\RequestExecutor;
use StellarWP\LicensingApiClient\Requests\License\Activate as ActivateRequest;
use StellarWP\LicensingApiClient\Requests\License\Alias\ImportAliases as ImportAliasesRequest;
use StellarWP\LicensingApiClient\Requests\License\Alias\RemoveAliases as RemoveAliasesRequest;
use StellarWP\LicensingApiClient\Requests\License\Deactivate as DeactivateRequest;
use StellarWP\LicensingApiClient\Requests\License\LicenseReference;
use StellarWP\LicensingApiClient\Requests\License\Listing\ListRequest;
use StellarWP\LicensingApiClient\Requests\License\RegenerateKey as RegenerateKeyRequest;
use StellarWP\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use StellarWP\LicensingApiClient\Resources\Contracts\LicensesResourceInterface;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;
use StellarWP\LicensingApiClient\Responses\License\Activate;
use StellarWP\LicensingApiClient\Responses\License\Alias\ImportAliases;
use StellarWP\LicensingApiClient\Responses\License\Alias\RemoveAliases;
use StellarWP\LicensingApiClient\Responses\License\Deactivate;
use StellarWP\LicensingApiClient\Responses\License\Listing\Listing;
use StellarWP\LicensingApiClient\Responses\License\RegenerateKey;
use StellarWP\LicensingApiClient\Responses\License\StatusChange;
use StellarWP\LicensingApiClient\Responses\License\Validate;

/**
 * Provides operations for the licenses API resource.
 *
 * @phpstan-type ValidatePayload array{
 *     license: array{key: string, status: string}|null,
 *     domain: string,
 *     is_production: bool,
 *     products: list<array{
 *         product_slug: string,
 *         status: string,
 *         is_valid: bool,
 *         entitlement: array{
 *             tier: string,
 *             site_limit: int,
 *             expiration_date: string,
 *             status: string,
 *             capabilities: list<string>
 *         }|null,
 *         activation: array{
 *             domain: string,
 *             activated_at: string
 *         }|null,
 *         available_entitlements?: list<array{
 *             tier: string,
 *             site_limit: int,
 *             active_count: int,
 *             available: int,
 *             capabilities: list<string>,
 *             status: string,
 *             expires: string
 *         }>
 *     }>
 * }
 * @phpstan-type ListingPayload array{
 *     licenses: list<array{
 *         license_key: string,
 *         identity_id: string,
 *         status: string,
 *         created_at: string,
 *         updated_at: string,
 *         subscriptions: list<array{
 *             product_slug: string,
 *             tier: string,
 *             site_limit: int,
 *             active_count: int,
 *             status: string,
 *             expiration_date: string,
 *             purchase_date: string
 *         }>
 *     }>,
 *     limit: int,
 *     next_cursor: ?int
 * }
 * @phpstan-import-type ActivatePayload from ActivateRequest
 * @phpstan-import-type DeactivatePayload from DeactivateRequest
 * @phpstan-import-type LicenseReferencePayload from LicenseReference
 * @phpstan-import-type RegenerateKeyPayload from RegenerateKeyRequest
 * @phpstan-import-type ImportAliasesPayload from ImportAliasesRequest
 * @phpstan-import-type RemoveAliasesPayload from RemoveAliasesRequest
 * @phpstan-type ActivateResponsePayload array{
 *     status: string,
 *     is_valid: bool,
 *     license: array{key: string, status: string}|null,
 *     entitlement: array{
 *         product_slug: string,
 *         tier: string,
 *         site_limit: int,
 *         expiration_date: string,
 *         status: string,
 *         capabilities: list<string>
 *     }|null,
 *     activation: array{
 *         domain: string,
 *         activated_at: string
 *     }|null
 * }
 * @phpstan-type DeactivateResponsePayload array{deactivated: bool}
 * @phpstan-type StatusChangePayload array{license_key: string, status: string}
 * @phpstan-type RegenerateKeyResponsePayload array{license_key: string}
 * @phpstan-type ImportAliasesResponsePayload array{
 *     imported: list<array{alias_key: string, product_slug?: string|null}>
 * }
 * @phpstan-type RemoveAliasesResponsePayload array{removed: int}
 */
final class LicensesResource implements LicensesResourceInterface
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
	 * @return Activate|ErrorResponse
	 */
	public function activate(ActivateRequest $request) {
		/** @var ActivatePayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->endpointFactory->make('/licenses/activate'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var ActivateResponsePayload $result */
		return Activate::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Deactivate|ErrorResponse
	 */
	public function deactivate(DeactivateRequest $request) {
		/** @var DeactivatePayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->endpointFactory->make('/licenses/deactivate'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var DeactivateResponsePayload $result */
		return Deactivate::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Listing|ErrorResponse
	 */
	public function list(ListRequest $request) {
		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->endpointFactory->make('/licenses'),
			$request->toQuery(),
			null,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var ListingPayload $result */
		return Listing::from($result);
	}

	/**
	 * @param list<string> $productSlugs
	 *
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Validate|ErrorResponse
	 */
	public function validate(string $key, array $productSlugs, string $domain) {
		$token = $this->authState->resolveTokenOrFail();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->endpointFactory->make('/licenses/validate'),
			[],
			[
				'key'           => $key,
				'product_slugs' => $productSlugs,
				'domain'        => $domain,
			],
			$token
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var ValidatePayload $result */
		return Validate::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return StatusChange|ErrorResponse
	 */
	public function suspend(LicenseReference $request) {
		return $this->changeLicenseStatus($this->endpointFactory->make('/licenses/suspend'), $request);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return StatusChange|ErrorResponse
	 */
	public function reinstate(LicenseReference $request) {
		return $this->changeLicenseStatus($this->endpointFactory->make('/licenses/reinstate'), $request);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return StatusChange|ErrorResponse
	 */
	public function ban(LicenseReference $request) {
		return $this->changeLicenseStatus($this->endpointFactory->make('/licenses/ban'), $request);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return RegenerateKey|ErrorResponse
	 */
	public function regenerateKey(RegenerateKeyRequest $request) {
		/** @var RegenerateKeyPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->endpointFactory->make('/licenses/regenerate-key'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var RegenerateKeyResponsePayload $result */
		return RegenerateKey::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return ImportAliases|ErrorResponse
	 */
	public function importAliases(ImportAliasesRequest $request) {
		/** @var ImportAliasesPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->endpointFactory->make('/licenses/aliases'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var ImportAliasesResponsePayload $result */
		return ImportAliases::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return RemoveAliases|ErrorResponse
	 */
	public function removeAliases(RemoveAliasesRequest $request) {
		/** @var RemoveAliasesPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'DELETE',
			$this->endpointFactory->make('/licenses/aliases'),
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var RemoveAliasesResponsePayload $result */
		return RemoveAliases::from($result);
	}

	/**
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return StatusChange|ErrorResponse
	 */
	private function changeLicenseStatus(\StellarWP\LicensingApiClient\Http\Endpoint $endpoint, LicenseReference $request) {
		/** @var LicenseReferencePayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$endpoint,
			[],
			$body,
			$this->authState->resolveRequiredTokenOrFail()
		);

		if ($result instanceof ErrorResponse) {
			return $result;
		}

		/** @var StatusChangePayload $result */
		return StatusChange::from($result);
	}
}
