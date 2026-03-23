<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use LiquidWeb\LicensingApiClient\Requests\Credit\CreatePool;
use LiquidWeb\LicensingApiClient\Requests\Credit\DeletePool as DeletePoolRequest;
use LiquidWeb\LicensingApiClient\Requests\Credit\UpdatePool;
use LiquidWeb\LicensingApiClient\Responses\Credit\DeletePool;
use LiquidWeb\LicensingApiClient\Responses\Credit\PoolCollection;
use LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects\CreditPool;
use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;

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
