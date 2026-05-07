<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\ValueObject;

final class ContentPayload {

  /**
   * @param array{type: string, json_ld: array<mixed>}|null $schema Schema.org data.
   */
  public function __construct(
    public readonly string $langcode,
    public readonly string $slug,
    public readonly string $title,
    public readonly string $subtitle,
    public readonly string $bodySummary,
    public readonly string $body,
    public readonly ?array $schema,
  ) {}

  public static function fromArray(string $langcode, array $data): self {
    return new self(
      langcode: $langcode,
      slug: (string) ($data['slug'] ?? ''),
      title: (string) ($data['title'] ?? ''),
      subtitle: (string) ($data['subtitle'] ?? ''),
      bodySummary: (string) ($data['body_summary'] ?? ''),
      body: (string) ($data['body'] ?? ''),
      schema: isset($data['schema']) ? (array) $data['schema'] : NULL,
    );
  }

}
