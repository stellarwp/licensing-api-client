<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient;

use LiquidWeb\LicensingApiClient\Http\ApiVersion;
use LiquidWeb\LicensingApiClient\Http\AuthContext;
use LiquidWeb\LicensingApiClient\Http\AuthState;
use LiquidWeb\LicensingApiClient\Http\Factories\ApiUriFactory;
use LiquidWeb\LicensingApiClient\Http\Factories\ResponseExceptionFactory;
use LiquidWeb\LicensingApiClient\Http\JsonDecoder;
use LiquidWeb\LicensingApiClient\Http\RequestBuilder;
use LiquidWeb\LicensingApiClient\Http\RequestExecutor;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsLedgerResource;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsPoolsResource;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsQuotasResource;
use LiquidWeb\LicensingApiClient\Resources\Credit\CreditsResource;
use LiquidWeb\LicensingApiClient\Resources\EntitlementsResource;
use LiquidWeb\LicensingApiClient\Resources\LicensesResource;
use LiquidWeb\LicensingApiClient\Resources\ProductsResource;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Builds a fully-wired API client from the transport dependencies.
 *
 * Use this if your application is not using a container to build dependencies.
 */
final class ApiBuilder
{
	private HttpClient $httpClient;

	private RequestFactoryInterface $requestFactory;

	private StreamFactoryInterface $streamFactory;

	private Config $config;

	public function __construct(
		HttpClient $httpClient,
		RequestFactoryInterface $requestFactory,
		StreamFactoryInterface $streamFactory,
		Config $config
	) {
		$this->httpClient     = $httpClient;
		$this->requestFactory = $requestFactory;
		$this->streamFactory  = $streamFactory;
		$this->config         = $config;
	}

	public function build(): Api {
		$authState       = new AuthState(new AuthContext(), $this->config->configuredToken);
		$apiUriFactory   = new ApiUriFactory($this->config, ApiVersion::default());
		$requestExecutor = $this->buildRequestExecutor();
		$creditsPools    = new CreditsPoolsResource($requestExecutor, $apiUriFactory, $authState);
		$creditsQuotas   = new CreditsQuotasResource($requestExecutor, $apiUriFactory, $authState);
		$creditsLedger   = new CreditsLedgerResource(
			$requestExecutor,
			$apiUriFactory,
			$authState
		);

		return new Api(
			$authState,
			new LicensesResource($requestExecutor, $apiUriFactory, $authState),
			new ProductsResource($requestExecutor, $apiUriFactory, $authState),
			new CreditsResource(
				$requestExecutor,
				$apiUriFactory,
				$authState,
				$creditsPools,
				$creditsQuotas,
				$creditsLedger
			),
			new EntitlementsResource($requestExecutor, $apiUriFactory, $authState)
		);
	}

	private function buildRequestExecutor(): RequestExecutor {
		$jsonDecoder = new JsonDecoder();

		return new RequestExecutor(
			$this->httpClient,
			new RequestBuilder(
				$this->requestFactory,
				$this->streamFactory
			),
			$jsonDecoder,
			new ResponseExceptionFactory($jsonDecoder)
		);
	}
}
