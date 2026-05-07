<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\ValueObject;

final class TagPayload {

  /**
   * @param string[] $existing EN names of tags that must already exist in Drupal.
   * @param array<array{en: string, es: string, ca: string}> $new New tags with all 3 translations.
   */
  public function __construct(
    public readonly array $existing,
    public readonly array $new,
  ) {}

  public static function fromArray(array $data): self {
    return new self(
      existing: array_values(array_filter((array) ($data['existing'] ?? []), 'is_string')),
      new: array_values((array) ($data['new'] ?? [])),
    );
  }

}
