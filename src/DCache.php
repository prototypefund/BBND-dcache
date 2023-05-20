<?php

declare(strict_types=1);

namespace Drupal\dcache;;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;

final class DCache implements DCacheInterface {

  /**
   * @var array<\Drupal\Core\Cache\CacheBackendInterface>
   */
  protected array $cacheBackends;

  private function __construct(array $cacheBackends) {
    $this->cacheBackends = $cacheBackends;
  }

  public static function create(CacheBackendInterface ...$cacheBackends) {
    return new static($cacheBackends);
  }

  public function lookupOrGenerate(CacheItemGeneratorInterface $generator) {
    return $this->doLookupOrGenerate($this->cacheBackends, $generator);
  }

  protected function doLookupOrGenerate(array $cacheBackends, CacheItemGeneratorInterface $generator) {
    if ($cacheBackends) {
      $cacheBackend = reset($cacheBackends);
      $cacheId = $generator->getCacheId();
      if ($cached = $cacheBackend->get($cacheId)) {
        return $cached->data;
      }
      else {
        $data = $this->doLookupOrGenerate(array_slice($cacheBackends, 1), $generator);
        $cacheBackend->set($cacheId, $data, Cache::PERMANENT, $generator->getCacheTags());
        return $data;
      }
    }
    else {
      return $generator->getData();
    }
  }

  /**
   * @param \Drupal\dcache\CacheItemListGeneratorInterface $itemListGenerator
   */
  public function lookupOrGenerateMultiple(CacheItemListGeneratorInterface $itemListGenerator): CacheItemList {
    return $this->doLookupOrGenerateMultiple($this->cacheBackends, $itemListGenerator);
  }

  /**
   * @param array<CacheBackendInterface> $cacheBackends
   */
  protected function doLookupOrGenerateMultiple(array $cacheBackends, CacheItemListGeneratorInterface $itemListGenerator): CacheItemList {
    if ($cacheBackends) {
      $cacheBackend = reset($cacheBackends);
      $cids = $itemListGenerator->getCacheIds();
      $items = CacheItemList::fromArraysByCacheId($cacheBackend->getMultiple($cids));
      // $cids now has the successfully fetched elements removed.
      if ($cids) {
        $missingItemsGenerator = $itemListGenerator->withCacheIds($cids);
        $missingItems = $this->doLookupOrGenerateMultiple(array_slice($cacheBackends, 1), $missingItemsGenerator);
        $cacheBackend->setMultiple($missingItems->toArraysByCacheId());
        $items->extend($missingItems);
      }
      return $items;
    }
    else {
      return $itemListGenerator->generateCacheItemList();
    }
  }

}
