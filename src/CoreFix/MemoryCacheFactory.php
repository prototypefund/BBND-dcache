<?php

namespace Drupal\dcache\CoreFix;

use Drupal\Core\Cache\CacheFactoryInterface;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Cache\MemoryCache\MemoryCache;

class MemoryCacheFactory implements CacheFactoryInterface, CacheTagsInvalidatorInterface {

  /**
   * Instantiated memory cache bins.
   *
   * @var \Drupal\Core\Cache\MemoryCache\MemoryCache[]
   */
  protected $bins = [];

  /**
   * {@inheritdoc}
   */
  public function get($bin) {
    if (!isset($this->bins[$bin])) {
      $this->bins[$bin] = new MemoryCache();
    }
    return $this->bins[$bin];
  }

  /**
   * {@inheritdoc}
   */
  public function invalidateTags(array $tags) {
    foreach ($this->bins as $bin) {
      $bin->invalidateTags($tags);
    }
  }

}
