<?php

declare(strict_types=1);

namespace Drupal\dcache;;

use Drupal\Core\Cache\CacheableDependencyInterface;

final class CacheItemList implements \IteratorAggregate, \ArrayAccess, \Countable {

  use ArrayAccessTrait;

  /**
   * @var array<CacheItem>
   */
  protected array $cacheItemList;

  function &getInnerArray(): array {
    return $this->cacheItemList;
  }

  /**
   * @param \Drupal\dcache;\CacheItem[] $cacheItemList
   */
  public function __construct(array $cacheItemList) {
    $this->cacheItemList = $cacheItemList;
  }

  public static function fromArray(array $array): self {
    return new self(array_map(fn(string $cid, array $item) => CacheItem::fromArray($cid, $item), array_keys($array), $array));
  }

  public function toArray(): array {
    return array_map(fn(CacheItem $item) => $item->toArray(), $this->cacheItemList);
  }

  public static function fromCacheablesByCacheId(CacheableDependencyInterface ...$cacheables): self {
    return new self(array_map(fn(CacheableDependencyInterface $cacheable, string $cid) => new CacheItem($cid, $cacheable, $cacheable->getCacheTags()), $cacheables, array_keys($cacheables)));
  }

}
