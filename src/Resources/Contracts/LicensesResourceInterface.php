<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use Generator;
use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Requests\License\Activate;
use LiquidWeb\LicensingApiClient\Requests\License\Alias\ImportAliases;
use LiquidWeb\LicensingApiClient\Requests\License\Alias\RemoveAliases;
use LiquidWeb\LicensingApiClient\Requests\License\Deactivate;
use LiquidWeb\LicensingApiClient\Requests\License\LicenseReference;
use LiquidWeb\LicensingApiClient\Requests\License\Listing\ListRequest;
use LiquidWeb\LicensingApiClient\Requests\License\RegenerateKey;
use LiquidWeb\LicensingApiClient\Responses\License\Activate as ActivateResponse;
use LiquidWeb\LicensingApiClient\Responses\License\Alias\ImportAliases as ImportAliasesResponse;
use LiquidWeb\LicensingApiClient\Responses\License\Alias\RemoveAliases as RemoveAliasesResponse;
use LiquidWeb\LicensingApiClient\Responses\License\Deactivate as DeactivateResponse;
use LiquidWeb\LicensingApiClient\Responses\License\Listing\Listing;
use LiquidWeb\LicensingApiClient\Responses\License\RegenerateKey as RegenerateKeyResponse;
use LiquidWeb\LicensingApiClient\Responses\License\StatusChange;
use LiquidWeb\LicensingApiClient\Responses\License\Validate;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Defines the licenses resource surface.
 */
interface LicensesResourceInterface
{
	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function list(ListRequest $request): Listing;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 *
	 * @return Generator<int, Listing, mixed, void>
	 */
	public function pages(ListRequest $request): Generator;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function activate(Activate $request): ActivateResponse;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function deactivate(Deactivate $request): DeactivateResponse;

	/**
	 * @param list<string> $productSlugs
	 *
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function validate(string $key, array $productSlugs, string $domain): Validate;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function suspend(LicenseReference $request): StatusChange;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function reinstate(LicenseReference $request): StatusChange;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function ban(LicenseReference $request): StatusChange;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function regenerateKey(RegenerateKey $request): RegenerateKeyResponse;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function importAliases(ImportAliases $request): ImportAliasesResponse;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function removeAliases(RemoveAliases $request): RemoveAliasesResponse;
}
