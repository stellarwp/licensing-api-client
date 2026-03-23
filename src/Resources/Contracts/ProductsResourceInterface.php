<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Resources\Contracts;

use LiquidWeb\LicensingApiClient\Responses\ErrorResponse;
use LiquidWeb\LicensingApiClient\Responses\Product\Catalog;

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
