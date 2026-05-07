<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\ValueObject;

final class DocumentPayload {

  /**
   * @param string $name Base name for the document set.
   * @param array<string, array{filename: string, content_base64: string}> $files Keyed by langcode.
   */
  public function __construct(
    public readonly string $name,
    public readonly array $files,
  ) {}

  public static function fromArray(array $data): self {
    return new self(
      name: (string) ($data['name'] ?? ''),
      files: (array) ($data['files'] ?? []),
    );
  }

}
