<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources\Contracts;

use Generator;
use StellarWP\LicensingApiClient\Requests\License\Activate;
use StellarWP\LicensingApiClient\Requests\License\Alias\ImportAliases;
use StellarWP\LicensingApiClient\Requests\License\Alias\RemoveAliases;
use StellarWP\LicensingApiClient\Requests\License\Deactivate;
use StellarWP\LicensingApiClient\Requests\License\LicenseReference;
use StellarWP\LicensingApiClient\Requests\License\Listing\ListRequest;
use StellarWP\LicensingApiClient\Requests\License\RegenerateKey;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;
use StellarWP\LicensingApiClient\Responses\License\Activate as ActivateResponse;
use StellarWP\LicensingApiClient\Responses\License\Alias\ImportAliases as ImportAliasesResponse;
use StellarWP\LicensingApiClient\Responses\License\Alias\RemoveAliases as RemoveAliasesResponse;
use StellarWP\LicensingApiClient\Responses\License\Deactivate as DeactivateResponse;
use StellarWP\LicensingApiClient\Responses\License\Listing\Listing;
use StellarWP\LicensingApiClient\Responses\License\RegenerateKey as RegenerateKeyResponse;
use StellarWP\LicensingApiClient\Responses\License\StatusChange;
use StellarWP\LicensingApiClient\Responses\License\Validate;

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
