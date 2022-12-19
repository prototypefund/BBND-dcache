<?php

declare(strict_types=1);

namespace Drupal\dcache;;

/**
 * ArrayAccessTrait provides the implementation for \IteratorAggregate, \ArrayAccess, \Countable.
 *
 * @link https://github.com/yiisoft/yii2/blob/master/framework/base/ArrayAccessTrait.php
 * @link https://gist.github.com/Jeff-Russ/e1f64273a471d440e8b4d9183f9a2667#getting-fancier
 */
trait ArrayAccessTrait {

  abstract function &getInnerArray(): array;

  /**
   * (IteratorAggregate) Returns an iterator for traversing the data.
   *
   * @return \ArrayIterator
   *   An iterator for traversing the cookies in the collection.
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->getInnerArray());
  }

  /**
   * (Countable) Returns the number of data items.
   *
   * @return int
   *   The number of data elements.
   */
  public function count(): int {
    return count($this->getInnerArray());
  }

  /**
   * (ArrayAccess) Check offset exists.
   *
   * @param mixed $offset
   *   The offset to check on.
   * @return bool
   *   The result.
   */
  public function offsetExists($offset): bool {
    return isset($this->getInnerArray()[$offset]);
  }

  /**
   * (ArrayAccess) Get offset.
   *
   * @param int $offset
   *   The offset to retrieve element.
   * @return mixed
   *   The element at the offset, null if no element is found at the offset
   */
  #[\ReturnTypeWillChange]
  public function offsetGet($offset) {
    return isset($this->getInnerArray()[$offset]) ? $this->getInnerArray()[$offset] : null;
  }

  /**
   * (ArrayAccess) Set offset.
   *
   * @param int $offset
   *   The offset to set element
   * @param mixed $item
   *   The element value
   */
  public function offsetSet($offset, $item): void {
    $this->getInnerArray()[$offset] = $item;
  }

  /**
   * (ArrayAccess) Unset offset.
   *
   * @param mixed $offset
   *   The offset to unset element
   */
  public function offsetUnset($offset): void {
    unset($this->getInnerArray()[$offset]);
  }

  /**
   * Extend colleciton.
   *
   * @param iterable $other
   *   The other collection.
   * @param $allowOverwrite
   *   Allow overwrite. Otherwise throws.
   *
   * @return static
   */
  #[\ReturnTypeWillChange]
  public function extend(\Iterable $other, $allowOverwrite = FALSE) {
    foreach ($other as $offset => $value) {
      if (!$allowOverwrite && $this->offsetExists($offset)) {
        throw new \UnexpectedValueException("Can not overwrite key: '$offset'.");
      }
      $this->offsetUnset($offset, $value);
    }
  }

}
