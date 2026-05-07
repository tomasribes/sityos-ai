<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\ValueObject;

final class IngestPayload {

  public const MODE_FULL = 'full';
  public const MODE_TAGS_ONLY = 'tags-only';
  public const MODE_UPDATE = 'update';

  public const TYPE_ARTICLE = 'article';
  public const TYPE_USE_CASE = 'use_case';

  public const SUPPORTED_LANGS = ['en', 'es', 'ca'];

  /**
   * @param array<string, ContentPayload> $content Keyed by langcode.
   */
  public function __construct(
    public readonly string $mode,
    public readonly string $contentType,
    public readonly TagPayload $tags,
    public readonly ?DocumentPayload $document,
    public readonly array $content,
    public readonly ?int $nodeId = NULL,
  ) {}

  public static function fromArray(array $data): self {
    $content = [];
    foreach ((array) ($data['content'] ?? []) as $lang => $langData) {
      $content[$lang] = ContentPayload::fromArray($lang, (array) $langData);
    }

    return new self(
      mode: (string) ($data['mode'] ?? ''),
      contentType: (string) ($data['content_type'] ?? ''),
      tags: TagPayload::fromArray((array) ($data['tags'] ?? [])),
      document: isset($data['document']) ? DocumentPayload::fromArray((array) $data['document']) : NULL,
      content: $content,
      nodeId: isset($data['node_id']) ? (int) $data['node_id'] : NULL,
    );
  }

}
