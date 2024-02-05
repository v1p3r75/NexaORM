<?php

namespace Nexa\Collections;

use ArrayAccess;
use Countable;

/**
 * Class Collection
 *
 * Represents a collection of items with various utility methods.
 */
class Collection implements Countable, ArrayAccess
{
    /**
     * Collection constructor.
     *
     * @param array $items The array of items to initialize the collection.
     */
    public function __construct(private array $items)
    {
    }

    /**
     * Returns the number of items in the collection.
     *
     * @return int The number of items in the collection.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Gets the value of the item with the specified key.
     *
     * @param string $key The key of the item.
     *
     * @return mixed|null The value of the item or null if the key is not found.
     */
    public function get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    /**
     * Gets all items in the collection, optionally applying a callback to each item.
     *
     * @param callable|null $callback The callback function to apply to each item.
     *
     * @return array The array of items.
     */
    public function all(?callable $callback = null): array
    {
        return $callback ? array_map($callback, $this->items) : $this->items;
    }

    /**
     * Magic method to get the value of an item using property access.
     *
     * @param string $name The name of the property (key) to retrieve.
     *
     * @return mixed|null The value of the item or null if the key is not found.
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Retrieves a random item from the collection.
     *
     * @return mixed The randomly selected item.
     */
    public function random(): mixed
    {
        $list = $this->all();
        shuffle($list);
        return count($list) > 0 ? $list[0] : null;
    }

    /**
     * Checks if an offset exists in the collection.
     *
     * @param mixed $offset The offset to check.
     *
     * @return bool True if the offset exists, false otherwise.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Gets the value of the item at the specified offset.
     *
     * @param mixed $offset The offset of the item.
     *
     * @return mixed|null The value of the item or null if the offset is not found.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    /**
     * Sets the value of the item at the specified offset.
     *
     * @param mixed $offset The offset of the item.
     * @param mixed $value The value to set.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[$offset] = $value;
    }

    /**
     * Unsets the item at the specified offset.
     *
     * @param mixed $offset The offset of the item to unset.
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * Retrieves the first item in the collection.
     *
     * @return mixed The first item in the collection.
     */
    public function first(): mixed
    {
        return reset($this->items);
    }

    /**
     * Retrieves the last item in the collection.
     *
     * @return mixed The last item in the collection.
     */
    public function last(): mixed
    {
        return end($this->items);
    }

    /**
     * Filters the collection based on a callback function.
     *
     * @param callable $callback The callback function used to filter the collection.
     *
     * @return Collection The filtered collection.
     */
    public function filter(callable $callback): Collection
    {
        return new self(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Applies a callback function to all items in the collection.
     *
     * @param callable $callback The callback function to apply to each item.
     *
     * @return Collection The new collection with the modified items.
     */
    public function map(callable $callback): Collection
    {
        return new self(array_map($callback, $this->items));
    }

    /**
     * Checks if the collection contains an item with the specified key.
     *
     * @param string $key The key to check for existence.
     *
     * @return bool True if the key exists, false otherwise.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Retrieves all values from the collection.
     *
     * @return array All values in the collection.
     */
    public function values(): array
    {
        return array_values($this->items);
    }

    /**
     * Retrieves all keys from the collection.
     *
     * @return array All keys in the collection.
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }

    /**
     * Checks if the collection is empty.
     *
     * @return bool True if the collection is empty, false otherwise.
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}