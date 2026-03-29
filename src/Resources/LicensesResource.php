<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources;

use Generator;
use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Http\Factories\ApiUriFactory;
use LiquidWeb\LicensingApiClient\Http\RequestExecutor;
use LiquidWeb\LicensingApiClient\Requests\License\Activate as ActivateRequest;
use LiquidWeb\LicensingApiClient\Requests\License\Alias\ImportAliases as ImportAliasesRequest;
use LiquidWeb\LicensingApiClient\Requests\License\Alias\RemoveAliases as RemoveAliasesRequest;
use LiquidWeb\LicensingApiClient\Requests\License\Deactivate as DeactivateRequest;
use LiquidWeb\LicensingApiClient\Requests\License\LicenseReference;
use LiquidWeb\LicensingApiClient\Requests\License\Listing\ListRequest;
use LiquidWeb\LicensingApiClient\Requests\License\RegenerateKey as RegenerateKeyRequest;
use LiquidWeb\LicensingApiClient\Resources\Concerns\RebindsAuthState;
use LiquidWeb\LicensingApiClient\Resources\Contracts\LicensesResourceInterface;
use LiquidWeb\LicensingApiClient\Responses\License\Activate;
use LiquidWeb\LicensingApiClient\Responses\License\Alias\ImportAliases;
use LiquidWeb\LicensingApiClient\Responses\License\Alias\RemoveAliases;
use LiquidWeb\LicensingApiClient\Responses\License\Deactivate;
use LiquidWeb\LicensingApiClient\Responses\License\Listing\Listing;
use LiquidWeb\LicensingApiClient\Responses\License\RegenerateKey;
use LiquidWeb\LicensingApiClient\Responses\License\StatusChange;
use LiquidWeb\LicensingApiClient\Responses\License\Validate;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Provides operations for the licenses API resource.
 *
 * @phpstan-type ValidatePayload array{
 *     license: array{license_key: string, status: string}|null,
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
 * @phpstan-import-type ActivatePayload from ActivateRequest
 * @phpstan-import-type DeactivatePayload from DeactivateRequest
 * @phpstan-import-type LicenseReferencePayload from LicenseReference
 * @phpstan-import-type RegenerateKeyPayload from RegenerateKeyRequest
 * @phpstan-import-type ImportAliasesPayload from ImportAliasesRequest
 * @phpstan-import-type RemoveAliasesPayload from RemoveAliasesRequest
 * @phpstan-type ActivateResponsePayload array{
 *     status: string,
 *     is_valid: bool,
 *     license: array{license_key: string, status: string}|null,
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
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function activate(ActivateRequest $request): Activate {
		/** @var ActivatePayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/licenses/activate'),
			[],
			$body,
			$this->authState->requiredToken()
		);

		/** @var ActivateResponsePayload $result */
		return Activate::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function deactivate(DeactivateRequest $request): Deactivate {
		/** @var DeactivatePayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/licenses/deactivate'),
			[],
			$body,
			$this->authState->requiredToken()
		);

		/** @var DeactivateResponsePayload $result */
		return Deactivate::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function list(ListRequest $request): Listing {
		$result = $this->requestExecutor->executeJson(
			'GET',
			$this->apiUriFactory->make('/licenses'),
			$request->toQuery(),
			null,
			$this->authState->requiredToken()
		);

		/** @var ListingPayload $result */
		return Listing::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Generator<int, Listing, mixed, void>
	 */
	public function pages(ListRequest $request): Generator {
		$page = $this->list($request);

		while (true) {
			yield $page;

			if ($page->links->next === null) {
				return;
			}

			$result = $this->requestExecutor->executeJson(
				'GET',
				$this->apiUriFactory->fromPaginationLink($page->links->next),
				[],
				null,
				$this->authState->requiredToken()
			);

			/** @var ListingPayload $result */
			$page = Listing::from($result);
		}
	}

	/**
	 * @param list<string> $productSlugs
	 *
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function validate(string $licenseKey, array $productSlugs, string $domain): Validate {
		$token = $this->authState->optionalToken();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/licenses/validate'),
			[],
			[
				'license_key'   => $licenseKey,
				'product_slugs' => $productSlugs,
				'domain'        => $domain,
			],
			$token
		);

		/** @var ValidatePayload $result */
		return Validate::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function suspend(LicenseReference $request): StatusChange {
		return $this->changeLicenseStatus('/licenses/suspend', $request);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function reinstate(LicenseReference $request): StatusChange {
		return $this->changeLicenseStatus('/licenses/reinstate', $request);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function ban(LicenseReference $request): StatusChange {
		return $this->changeLicenseStatus('/licenses/ban', $request);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function regenerateKey(RegenerateKeyRequest $request): RegenerateKey {
		/** @var RegenerateKeyPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/licenses/regenerate-key'),
			[],
			$body,
			$this->authState->requiredToken()
		);

		/** @var RegenerateKeyResponsePayload $result */
		return RegenerateKey::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function importAliases(ImportAliasesRequest $request): ImportAliases {
		/** @var ImportAliasesPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make('/licenses/aliases'),
			[],
			$body,
			$this->authState->requiredToken()
		);

		/** @var ImportAliasesResponsePayload $result */
		return ImportAliases::from($result);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function removeAliases(RemoveAliasesRequest $request): RemoveAliases {
		/** @var RemoveAliasesPayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'DELETE',
			$this->apiUriFactory->make('/licenses/aliases'),
			[],
			$body,
			$this->authState->requiredToken()
		);

		/** @var RemoveAliasesResponsePayload $result */
		return RemoveAliases::from($result);
	}

	protected function rebindWithAuthState(AuthState $authState): self {
		return new self($this->requestExecutor, $this->apiUriFactory, $authState);
	}

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	private function changeLicenseStatus(string $path, LicenseReference $request): StatusChange {
		/** @var LicenseReferencePayload $body */
		$body = $request->toArray();

		$result = $this->requestExecutor->executeJson(
			'POST',
			$this->apiUriFactory->make($path),
			[],
			$body,
			$this->authState->requiredToken()
		);

		/** @var StatusChangePayload $result */
		return StatusChange::from($result);
	}
}
