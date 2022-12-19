<?php

namespace Drupal\dcache;;

use Drupal\Core\Cache\CacheBackendInterface;

interface DCacheFactoryInterface {

  public function get(CacheBackendInterface ...$cacheBackends);

}
