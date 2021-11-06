<?php
declare(strict_types=1);

namespace Ricardo1709\Cache;

use DateInterval;
use DateTime;
use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
	protected $value;

	protected $key;

	protected $hit;

	/**
	 * The expriration date.
	 *
	 * @var DateTime
	 */
	protected $expiration;

	public function __construct(string $key)
	{
		$this->key = $key;

		$this->expiration = null;
		$this->value = null;
		$this->hit = false;
	}

	/**
     * Returns the key for the current cache item.
     *
     * The key is loaded by the Implementing Library, but should be available to
     * the higher level callers when needed.
     *
     * @return string
     *   The key string for this cache item.
     */
    public function getKey(): string
	{
		return $this->key;
	}

    /**
     * Retrieves the value of the item from the cache associated with this object's key.
     *
     * The value returned must be identical to the value originally stored by set().
     *
     * If isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed
     *   The value corresponding to this cache item's key, or null if not found.
     */
    public function get(): mixed
	{
		return $this->isHit() ? $this->value : null;
	}

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     *
     * @return bool
     *   True if the request resulted in a cache hit. False otherwise.
     */
    public function isHit(): bool
	{
		return $this->hit && ($this->expiration === null || $this->expiration > new DateTime());
	}

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * @param mixed $value
     *   The serializable value to be stored.
     *
     * @return static
     *   The invoked object.
     */
    public function set(mixed $value): static
	{
		$this->hit = true;
		$this->value = $value;

		return $this;
	}

    /**
     * Sets the expiration time for this cache item.
     *
     * @param ?\DateTimeInterface $expiration
     *   The point in time after which the item MUST be considered expired.
     *   If null is passed explicitly, a default value MAY be used. If none is set,
     *   the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function expiresAt(?\DateTimeInterface $expiration): static
	{
		$this->expiration = $expiration;
		return $this;
	}

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval|null $time
     *   The period of time from the present after which the item MUST be considered
     *   expired. An integer parameter is understood to be the time in seconds until
     *   expiration. If null is passed explicitly, a default value MAY be used.
     *   If none is set, the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function expiresAfter(int|\DateInterval|null $time): static
	{
		if ($time === null) {
			return $this->expiresAt($time);
		}

		if (is_integer($time)) {
			$time = new DateInterval('PT' . $time . 'S');
		}

		return $this->expiresAt((new DateTime())->add($time));
	}
}