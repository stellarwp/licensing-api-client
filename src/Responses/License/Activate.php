<?php declare(strict_types=1);

namespace StellarWP\LicensingApiClient\Responses\License;

use StellarWP\LicensingApiClient\Responses\Contracts\Response;
use StellarWP\LicensingApiClient\Responses\License\ValueObjects\Activation;
use StellarWP\LicensingApiClient\Responses\License\ValueObjects\ActivationEntitlement;
use StellarWP\LicensingApiClient\Responses\License\ValueObjects\LicenseSummary;

/**
 * Represents a license activation response.
 *
 * @implements Response<array{
 *     status: string,
 *     is_valid: bool,
 *     license: array{key: string, status: string}|null,
 *     entitlement: array{
 *         product_slug: string,
 *         tier: string,
 *         site_limit: int,
 *         expiration_date: string,
 *         status: string,
 *         capabilities: list<string>
 *     }|null,
 *     activation: array{
 *         domain: string,
 *         activated_at: string
 *     }|null
 * }>
 */
final class Activate implements Response
{
	public string $status;

	public bool $isValid;

	public ?LicenseSummary $license;

	public ?ActivationEntitlement $entitlement;

	public ?Activation $activation;

	private function __construct(
		string $status,
		bool $isValid,
		?LicenseSummary $license,
		?ActivationEntitlement $entitlement,
		?Activation $activation
	) {
		$this->status      = $status;
		$this->isValid     = $isValid;
		$this->license     = $license;
		$this->entitlement = $entitlement;
		$this->activation  = $activation;
	}

	/**
	 * @param array{
	 *     status: string,
	 *     is_valid: bool,
	 *     license: array{key: string, status: string}|null,
	 *     entitlement: array{
	 *         product_slug: string,
	 *         tier: string,
	 *         site_limit: int,
	 *         expiration_date: string,
	 *         status: string,
	 *         capabilities: list<string>
	 *     }|null,
	 *     activation: array{
	 *         domain: string,
	 *         activated_at: string
	 *     }|null
	 * } $attributes
	 */
	public static function from(array $attributes): self {
		$license     = $attributes['license'] ?? null;
		$entitlement = $attributes['entitlement'] ?? null;
		$activation  = $attributes['activation'] ?? null;

		return new self(
			$attributes['status'],
			$attributes['is_valid'],
			$license ? LicenseSummary::from($license) : null,
			$entitlement ? ActivationEntitlement::from($entitlement) : null,
			$activation ? Activation::from($activation) : null
		);
	}

	public function toArray(): array {
		return [
			'status'      => $this->status,
			'is_valid'    => $this->isValid,
			'license'     => $this->license ? $this->license->toArray() : null,
			'entitlement' => $this->entitlement ? $this->entitlement->toArray() : null,
			'activation'  => $this->activation ? $this->activation->toArray() : null,
		];
	}
}
