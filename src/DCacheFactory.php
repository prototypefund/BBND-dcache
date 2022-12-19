<?php

declare(strict_types=1);

namespace Drupal\dcache;;
use Drupal\Core\Cache\CacheBackendInterface;

class DCacheFactory implements DCacheFactoryInterface {

  public function get(CacheBackendInterface ...$cacheBackends) {
    return DCache::create(...$cacheBackends);
  }

}
