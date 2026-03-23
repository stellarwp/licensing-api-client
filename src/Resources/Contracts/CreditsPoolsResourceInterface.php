<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources\Contracts;

use StellarWP\LicensingApiClient\Requests\Credit\CreatePool;
use StellarWP\LicensingApiClient\Requests\Credit\DeletePool as DeletePoolRequest;
use StellarWP\LicensingApiClient\Requests\Credit\UpdatePool;
use StellarWP\LicensingApiClient\Responses\Credit\DeletePool;
use StellarWP\LicensingApiClient\Responses\Credit\PoolCollection;
use StellarWP\LicensingApiClient\Responses\Credit\ValueObjects\CreditPool;
use StellarWP\LicensingApiClient\Responses\ErrorResponse;

/**
 * Defines the credits pools resource surface.
 */
interface CreditsPoolsResourceInterface
{
	/**
	 * @return PoolCollection|ErrorResponse
	 */
	public function list(string $key, bool $active = false);

	/**
	 * @return CreditPool|ErrorResponse
	 */
	public function create(CreatePool $request);

	/**
	 * @return CreditPool|ErrorResponse
	 */
	public function update(UpdatePool $request);

	/**
	 * @return DeletePool|ErrorResponse
	 */
	public function delete(DeletePoolRequest $request);
}
