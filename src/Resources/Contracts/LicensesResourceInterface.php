<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use Generator;
use LiquidWeb\LicensingApiClient\Requests\License\Activate;
use LiquidWeb\LicensingApiClient\Requests\License\Alias\ImportAliases;
use LiquidWeb\LicensingApiClient\Requests\License\Alias\RemoveAliases;
use LiquidWeb\LicensingApiClient\Requests\License\Deactivate;
use LiquidWeb\LicensingApiClient\Requests\License\LicenseReference;
use LiquidWeb\LicensingApiClient\Requests\License\Listing\ListRequest;
use LiquidWeb\LicensingApiClient\Requests\License\RegenerateKey;
use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;
use LiquidWeb\LicensingApiClient\Responses\License\Activate as ActivateResponse;
use LiquidWeb\LicensingApiClient\Responses\License\Alias\ImportAliases as ImportAliasesResponse;
use LiquidWeb\LicensingApiClient\Responses\License\Alias\RemoveAliases as RemoveAliasesResponse;
use LiquidWeb\LicensingApiClient\Responses\License\Deactivate as DeactivateResponse;
use LiquidWeb\LicensingApiClient\Responses\License\Listing\Listing;
use LiquidWeb\LicensingApiClient\Responses\License\RegenerateKey as RegenerateKeyResponse;
use LiquidWeb\LicensingApiClient\Responses\License\StatusChange;
use LiquidWeb\LicensingApiClient\Responses\License\Validate;

/**
 * Defines the licenses resource surface.
 */
interface LicensesResourceInterface
{
	/**
	 * @return Listing|ErrorResponse
	 */
	public function list(ListRequest $request);

	/**
	 * @return Generator<int, Listing|ErrorResponse, mixed, void>
	 */
	public function pages(ListRequest $request): Generator;

	/**
	 * @return ActivateResponse|ErrorResponse
	 */
	public function activate(Activate $request);

	/**
	 * @return DeactivateResponse|ErrorResponse
	 */
	public function deactivate(Deactivate $request);

	/**
	 * @param list<string> $productSlugs
	 *
	 * @return Validate|ErrorResponse
	 */
	public function validate(string $key, array $productSlugs, string $domain);

	/**
	 * @return StatusChange|ErrorResponse
	 */
	public function suspend(LicenseReference $request);

	/**
	 * @return StatusChange|ErrorResponse
	 */
	public function reinstate(LicenseReference $request);

	/**
	 * @return StatusChange|ErrorResponse
	 */
	public function ban(LicenseReference $request);

	/**
	 * @return RegenerateKeyResponse|ErrorResponse
	 */
	public function regenerateKey(RegenerateKey $request);

	/**
	 * @return ImportAliasesResponse|ErrorResponse
	 */
	public function importAliases(ImportAliases $request);

	/**
	 * @return RemoveAliasesResponse|ErrorResponse
	 */
	public function removeAliases(RemoveAliases $request);
}
