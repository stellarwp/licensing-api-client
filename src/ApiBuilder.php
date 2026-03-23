<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient;

use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use StellarWP\LicensingApiClient\Http\ApiVersion;
use StellarWP\LicensingApiClient\Http\AuthContext;
use StellarWP\LicensingApiClient\Http\AuthState;
use StellarWP\LicensingApiClient\Http\Factories\EndpointFactory;
use StellarWP\LicensingApiClient\Http\JsonDecoder;
use StellarWP\LicensingApiClient\Http\RequestBuilder;
use StellarWP\LicensingApiClient\Http\RequestExecutor;
use StellarWP\LicensingApiClient\Resources\Credit\CreditsLedgerResource;
use StellarWP\LicensingApiClient\Resources\Credit\CreditsPoolsResource;
use StellarWP\LicensingApiClient\Resources\Credit\CreditsQuotasResource;
use StellarWP\LicensingApiClient\Resources\Credit\CreditsResource;
use StellarWP\LicensingApiClient\Resources\EntitlementsResource;
use StellarWP\LicensingApiClient\Resources\LicensesResource;
use StellarWP\LicensingApiClient\Resources\ProductsResource;

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
		$endpointFactory = new EndpointFactory(ApiVersion::default());
		$requestExecutor = $this->buildRequestExecutor();
		$creditsPools    = new CreditsPoolsResource($requestExecutor, $endpointFactory, $authState);
		$creditsQuotas   = new CreditsQuotasResource($requestExecutor, $endpointFactory, $authState);
		$creditsLedger   = new CreditsLedgerResource($requestExecutor, $endpointFactory, $authState);

		return new Api(
			$authState,
			new LicensesResource($requestExecutor, $endpointFactory, $authState),
			new ProductsResource($requestExecutor, $endpointFactory, $authState),
			new CreditsResource($requestExecutor, $endpointFactory, $authState, $creditsPools, $creditsQuotas, $creditsLedger),
			new EntitlementsResource($requestExecutor, $endpointFactory, $authState)
		);
	}

	private function buildRequestExecutor(): RequestExecutor {
		return new RequestExecutor(
			$this->httpClient,
			new RequestBuilder(
				$this->requestFactory,
				$this->streamFactory,
				$this->config
			),
			new JsonDecoder()
		);
	}
}
