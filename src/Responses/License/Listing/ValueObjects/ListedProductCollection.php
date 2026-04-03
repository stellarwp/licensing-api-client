<?php declare(strict_types=1);

namespace LiquidWeb\LicensingApiClient\Responses\License\Listing\ValueObjects;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use LiquidWeb\LicensingApiClient\Responses\Contracts\Response;
use LogicException;
use Traversable;

/**
 * Immutable collection of products nested under a license listing response.
 *
 * @implements Response<list<array{
 *     product_slug: string,
 *     tier: string,
 *     status: string,
 *     expires: string,
 *     capabilities: list<string>,
 *     activations: array{
 *         site_limit: int,
 *         active_count: int,
 *         over_limit: bool,
 *         domains: list<string>
 *     }
 * }>>
 * @implements ArrayAccess<int, ListedProduct>
 * @implements IteratorAggregate<int, ListedProduct>
 */
final class ListedProductCollection implements ArrayAccess, Countable, IteratorAggregate, Response
{
	/**
	 * @var list<ListedProduct>
	 */
	private array $items;

	/**
	 * @param list<ListedProduct> $items
	 */
	private function __construct(array $items) {
		$this->items = array_values($items);
	}

	/**
	 * @param list<array{
	 *     product_slug: string,
	 *     tier: string,
	 *     status: string,
	 *     expires: string,
	 *     capabilities: list<string>,
	 *     activations: array{
	 *         site_limit: int,
	 *         active_count: int,
	 *         over_limit: bool,
	 *         domains: list<string>
	 *     }
	 * }> $attributes
	 */
	public static function from(array $attributes): self {
		return new self(array_map(
			static fn (array $entry): ListedProduct => ListedProduct::from($entry),
			$attributes
		));
	}

	public function forProduct(string $productSlug): self {
		return new self(array_values(array_filter(
			$this->items,
			static fn (ListedProduct $entry): bool => $entry->productSlug === $productSlug
		)));
	}

	public function active(): self {
		return new self(array_values(array_filter(
			$this->items,
			static fn (ListedProduct $entry): bool => $entry->isActive()
		)));
	}

	public function first(): ?ListedProduct {
		return $this->items[0] ?? null;
	}

	/**
	 * @return list<ListedProduct>
	 */
	public function all(): array {
		return $this->items;
	}

	public function hasCapability(string $capability): bool {
		foreach ($this->items as $entry) {
			if ($entry->hasCapability($capability)) {
				return true;
			}
		}

		return false;
	}

	public function count(): int {
		return count($this->items);
	}

	/**
	 * @return ArrayIterator<int, ListedProduct>
	 */
	public function getIterator(): Traversable {
		return new ArrayIterator($this->items);
	}

	/**
	 * @param int|string|mixed $offset
	 */
	public function offsetExists($offset): bool {
		return is_int($offset) && isset($this->items[$offset]);
	}

	/**
	 * @param int|string|mixed $offset
	 */
	public function offsetGet($offset): ?ListedProduct {
		if (! is_int($offset)) {
			return null;
		}

		return $this->items[$offset] ?? null;
	}

	public function toArray(): array {
		return array_map(
			static fn (ListedProduct $entry): array => $entry->toArray(),
			$this->items
		);
	}

	/**
	 * @param int|string|mixed $offset
	 * @param mixed $value Ignored because the collection is immutable.
	 */
	public function offsetSet($offset, $value): void {
		throw new LogicException('ListedProductCollection is immutable.');
	}

	/**
	 * @param int|string|mixed $offset
	 */
	public function offsetUnset($offset): void {
		throw new LogicException('ListedProductCollection is immutable.');
	}
}
