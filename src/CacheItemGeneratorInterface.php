<?php

declare(strict_types=1);

namespace Drupal\dcache;;

interface CacheItemGeneratorInterface {

  /**
   * @return string
   *   The cache ID.
   */
  public function getCacheId(): string;

  /**
   * @return array<string>
   *   The cache tags.
   */
  public function getCacheTags(): array;

  /**
   * @return mixed
   *   The data.
   */
  public function getData();

}
