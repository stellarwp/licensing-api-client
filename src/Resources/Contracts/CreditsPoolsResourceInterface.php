<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Requests\Credit\CreatePool;
use LiquidWeb\LicensingApiClient\Requests\Credit\DeletePool as DeletePoolRequest;
use LiquidWeb\LicensingApiClient\Requests\Credit\UpdatePool;
use LiquidWeb\LicensingApiClient\Responses\Credit\DeletePool;
use LiquidWeb\LicensingApiClient\Responses\Credit\PoolCollection;
use LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects\CreditPool;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Defines the credits pools resource surface.
 */
interface CreditsPoolsResourceInterface
{
	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function list(string $key, bool $active = false): PoolCollection;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function create(CreatePool $request): CreditPool;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function update(UpdatePool $request): CreditPool;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function delete(DeletePoolRequest $request): DeletePool;
}
