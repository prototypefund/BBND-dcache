<?php

declare(strict_types=1);

namespace Drupal\dcache;;
final class CacheItem {

  protected string $cid;

  protected $data;

  protected array $tags;

  /**
   * @param string $cid
   * @param $data
   * @param array $tags
   */
  public function __construct(string $cid, $data, array $tags) {
    $this->cid = $cid;
    $this->data = $data;
    $this->tags = $tags;
  }

  /**
   * @return string
   */
  public function getCid(): string {
    return $this->cid;
  }

  /**
   * @return mixed
   */
  public function getData() {
    return $this->data;
  }

  /**
   * @return array<string>
   */
  public function getTags(): array {
    return $this->tags;
  }

  public static function fromArray(string $cid, array $array): self {
    return new self($cid, $array['data'], $array['tags']);
  }

  public function toArray(): array {
    return [
      'data' => $this->data,
      'tags' => $this->tags,
    ];
  }

}
