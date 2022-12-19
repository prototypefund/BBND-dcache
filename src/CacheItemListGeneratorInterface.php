<?php

namespace Drupal\dcache;;

interface CacheItemListGeneratorInterface {

  /**
   * @return array<string>
   *   The cache IDs.
   */
  public function getCacheIds(): array;

  /**
   * Get a new instance for the subset.
   *
   * @param array $cacheIds
   *   The cache IDs, having same keys as returned from ::getCacheIdList.
   *
   * @return static
   *   A new instance for the subset.
   */
  public function forCacheIds(array $cacheIds): self;

  /**
   * Generate cache items.
   *
   * @return \Drupal\dcache;\CacheItemList
   *   The generated cache items.
   */
  public function generateCacheItemList(): CacheItemList;

}
