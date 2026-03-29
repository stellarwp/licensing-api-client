<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use JsonException;
use LiquidWeb\LicensingApiClient\Exceptions\Contracts\ApiErrorExceptionInterface;
use LiquidWeb\LicensingApiClient\Exceptions\MissingAuthenticationException;
use LiquidWeb\LicensingApiClient\Exceptions\UnexpectedResponseException;
use LiquidWeb\LicensingApiClient\Requests\Credit\SetQuota;
use LiquidWeb\LicensingApiClient\Responses\Credit\DeleteQuota;
use LiquidWeb\LicensingApiClient\Responses\Credit\QuotaCollection;
use LiquidWeb\LicensingApiClient\Responses\Credit\ValueObjects\SiteQuota;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Defines the credits quotas resource surface.
 */
interface CreditsQuotasResourceInterface
{
	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function list(string $licenseKey): QuotaCollection;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function set(SetQuota $request): SiteQuota;

	/**
	 * @throws ApiErrorExceptionInterface
	 * @throws MissingAuthenticationException
	 * @throws UnexpectedResponseException
	 * @throws ClientExceptionInterface
	 * @throws JsonException
	 */
	public function delete(string $licenseKey, string $domain, string $creditType): DeleteQuota;
}
