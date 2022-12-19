<?php

namespace Drupal\dcache;;

/**
 * Deterministic cache interface.
 *
 * This interface encapsulates the conditions for a cache that can be chained
 * without requiring coordination. Stated differently: The problem with chaining
 * instances of CacheBackendInterface is that any write invalidates all cache
 * items, and ChainedFastBackend suffers from that.
 * It can be shown that under reasonable circumstances, any "well-behaved" cache
 * CAN be chained without need for coordination, which means that a write to the
 * cache does NOT clear the whole cache.
 * This interface encapsulates that conditions.
 * In addition to using the interface, code that uses it MUST comply to the
 * following "well-behaving" prerequisites:
 * - The $calculate callable and the $tags cache tags must be a function of the
 *   $cid cache ID alone. In other words, any $cid MUST always correspond to the
 *   same $calculate and $tags.
 */
interface DCacheInterface {

  /**
   * Lookup or retrieve a single item.
   *
   * @param string $cid
   *   The cache ID.
   * @param callable $calculate
   *   The callable that calculates the data items. It receives no arguments,
   *   and MUST return the calculated data item.
   *   See also the requirements on $calculate and $tags on the interface.
   * @param array<string> $tags
   *   The cache tags.
   *   See also the requirements on $calculate and $tags on the interface.
   *
   * @return mixed
   *   The resulting data item.
   *
   * @see \Drupal\dcache;\DCacheInterface
   */
  public function lookupOrGenerate(CacheItemGeneratorInterface $generator);

  /**
   * Lookup or retrieve multiple items.
   *
   * @param array $cids
   *   The cache IDs. Keys are irrelevant, but preserved. So e.g. in an entity
   *   cache the keys kay be the IDs of the entities.
   * @param callable $calculate
   *   The callable that calculates the data items. It receives a subset of the
   *   $cids as argument. It MUST return data items keyed by $cid, and the keys
   *   MUST be the same as the value of the arguments that the callback
   *   received.
   *   See also the requirements on $calculate and $tags on the interface.
   * @param array<string> $tags
   *   The cache tags.
   *   See also the requirements on $calculate and $tags on the interface.
   *
   * @return array<mixed, string>
   *   The resulting data items, keyed by cid.
   *
   * @see \Drupal\dcache;\DCacheInterface
   */
  public function lookupOrGenerateMultiple(CacheItemListGeneratorInterface $itemListGenerator): array;

}
