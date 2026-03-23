<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Resources\Contracts;

use StellarWP\LicensingApiClient\Responses\ErrorResponse;
use StellarWP\LicensingApiClient\Responses\Product\Catalog;

/**
 * Defines the products resource surface.
 */
interface ProductsResourceInterface
{
	/**
	 * @return Catalog|ErrorResponse
	 */
	public function catalog(string $key, ?string $domain = null);
}
