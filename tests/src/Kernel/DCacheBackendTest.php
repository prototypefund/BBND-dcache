<?php

declare(strict_types=1);

namespace Drupal\Tests\dcache\Kernel;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\dcache\CacheItemGeneratorInterface;
use Drupal\dcache\DCacheInterface;
use Drupal\KernelTests\KernelTestBase;

/**
 * DCache test
 *
 * @group dcache
 */
final class DCacheBackendTest extends KernelTestBase {

  public static $modules = ['dcache'];

  protected CacheBackendInterface $memoryCache;

  protected CacheBackendInterface $persistentCache;

  protected DCacheInterface $dCache;

  protected CacheTagsInvalidatorInterface $cacheTagsInvalidator;

  protected function setUp(): void {
    parent::setUp();
    $this->dCache = \Drupal::service('dcache.bin.default_memory_persistent');
    $this->invalidator = \Drupal::service('cache_tags.invalidator');
    $this->memoryCache = \Drupal::service('cache.dcache_default_memory');
    $this->persistentCache = \Drupal::service('cache.dcache_default_persistent');
    $this->cacheTagsInvalidator = \Drupal::service('cache_tags.invalidator');
  }

  public function testDCacheBin() {
    // Generate result and verify it's in all caches.
    $result = $this->dCache->lookupOrGenerate($this->getItemGenerator());
    $this->assertSame(42, $result);
    $this->assertSame(42, $this->memoryCache->get('the-answer')->data);
    $this->assertSame(42, $this->persistentCache->get('the-answer')->data);

    // Verify that caches are used.
    $result = $this->dCache->lookupOrGenerate($this->getThrowingItemGenerator());
    $this->assertSame(42, $result);

    // Delete memory cache item and verify that persistent cache is used.
    $this->memoryCache->delete('the-answer');
    $result = $this->dCache->lookupOrGenerate($this->getThrowingItemGenerator());
    $this->assertSame(42, $result);

    // Verify that memory cache is set again.
    $this->assertSame(42, $this->memoryCache->get('the-answer')->data);
    $this->assertSame(42, $this->persistentCache->get('the-answer')->data);

    // Delete persistent cache item and verify that memory cache is used.
    $this->persistentCache->delete('the-answer');
    $result = $this->dCache->lookupOrGenerate($this->getThrowingItemGenerator());
    $this->assertSame(42, $result);

    // ...which will NOT set persistent cache, so bring in proper state again.
    $this->memoryCache->delete('the-answer');
    $result = $this->dCache->lookupOrGenerate($this->getItemGenerator());
    $this->assertSame(42, $result);
    // Both caches should be set again now.
    $this->assertSame(42, $this->memoryCache->get('the-answer')->data);
    $this->assertSame(42, $this->persistentCache->get('the-answer')->data);

    // Verify that invalidation works.
    $this->cacheTagsInvalidator->invalidateTags(['douglas_adams']);
    $this->assertSame(FALSE, $this->memoryCache->get('the-answer'));
    $this->assertSame(FALSE, $this->persistentCache->get('the-answer'));
  }

  public function getItemGenerator(): CacheItemGeneratorInterface {
    return new class() implements CacheItemGeneratorInterface {

      public function getCacheId(): string {
        return 'the-answer';
      }

      public function getCacheTags(): array {
        return ['douglas_adams'];
      }

      public function getData() {
        return 42;
      }

    };
  }

  public function getThrowingItemGenerator(): CacheItemGeneratorInterface {
    return new class() implements CacheItemGeneratorInterface {

      public function getCacheId(): string {
        return 'the-answer';
      }

      public function getCacheTags(): array {
        return ['douglas_adams'];
      }

      public function getData() {
        throw new \RuntimeException();
      }

    };
  }

}
