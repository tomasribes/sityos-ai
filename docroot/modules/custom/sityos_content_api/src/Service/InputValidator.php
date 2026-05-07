<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\Service;

use Drupal\Component\Utility\Xss;
use Drupal\sityos_content_api\Exception\IngestValidationException;
use Drupal\sityos_content_api\ValueObject\IngestPayload;
use Psr\Log\LoggerInterface;

final class InputValidator {

  private const SCHEMA_TYPES = ['HowTo', 'Article', 'TechArticle'];
  private const MAX_PDF_BYTES = 20 * 1024 * 1024;
  private const MAX_TITLE_LENGTH = 255;
  private const MAX_SUBTITLE_LENGTH = 255;

  public function __construct(
    private readonly LoggerInterface $logger,
    private readonly int $maxPdfSizeMb = 20,
  ) {}

  public function validate(IngestPayload $payload): void {
    $errors = [];

    $errors = array_merge($errors, $this->validateMode($payload));
    $errors = array_merge($errors, $this->validateContentType($payload));
    $errors = array_merge($errors, $this->validateTags($payload));
    $errors = array_merge($errors, $this->validateDocument($payload));
    $errors = array_merge($errors, $this->validateContent($payload));

    if (!empty($errors)) {
      throw new IngestValidationException($errors);
    }
  }

  private function validateMode(IngestPayload $payload): array {
    $errors = [];
    $valid = [IngestPayload::MODE_FULL, IngestPayload::MODE_TAGS_ONLY, IngestPayload::MODE_UPDATE];

    if (!in_array($payload->mode, $valid, TRUE)) {
      $errors[] = ['field' => 'mode', 'message' => sprintf('Invalid mode "%s". Must be one of: %s', $payload->mode, implode(', ', $valid))];
    }

    if ($payload->mode === IngestPayload::MODE_UPDATE && $payload->nodeId === NULL) {
      $errors[] = ['field' => 'node_id', 'message' => 'node_id is required for mode "update"'];
    }

    return $errors;
  }

  private function validateContentType(IngestPayload $payload): array {
    $errors = [];
    $valid = [IngestPayload::TYPE_ARTICLE, IngestPayload::TYPE_USE_CASE];

    if (!in_array($payload->contentType, $valid, TRUE)) {
      $errors[] = ['field' => 'content_type', 'message' => sprintf('Invalid content_type "%s". Must be one of: %s', $payload->contentType, implode(', ', $valid))];
    }

    if ($payload->contentType === IngestPayload::TYPE_USE_CASE && $payload->document !== NULL) {
      $errors[] = ['field' => 'document', 'message' => 'document must be omitted for content_type "use_case"'];
    }

    if ($payload->contentType === IngestPayload::TYPE_ARTICLE
      && $payload->mode === IngestPayload::MODE_FULL
      && $payload->document === NULL) {
      $errors[] = ['field' => 'document', 'message' => 'document is required for content_type "article" in mode "full"'];
    }

    return $errors;
  }

  private function validateTags(IngestPayload $payload): array {
    $errors = [];

    foreach ($payload->tags->new as $i => $tag) {
      foreach (IngestPayload::SUPPORTED_LANGS as $lang) {
        if (empty($tag[$lang])) {
          $errors[] = ['field' => "tags.new[$i].$lang", 'message' => "Tag translation for '$lang' is required"];
        }
        elseif (mb_strlen((string) $tag[$lang]) > self::MAX_TITLE_LENGTH) {
          $errors[] = ['field' => "tags.new[$i].$lang", 'message' => "Tag exceeds 255 character limit"];
        }
      }
    }

    return $errors;
  }

  private function validateDocument(IngestPayload $payload): array {
    $errors = [];

    if ($payload->document === NULL) {
      return $errors;
    }

    if (empty($payload->document->name)) {
      $errors[] = ['field' => 'document.name', 'message' => 'document.name is required'];
    }

    foreach (IngestPayload::SUPPORTED_LANGS as $lang) {
      $file = $payload->document->files[$lang] ?? NULL;

      if ($file === NULL) {
        $errors[] = ['field' => "document.files.$lang", 'message' => "PDF for language '$lang' is required"];
        continue;
      }

      if (empty($file['filename'])) {
        $errors[] = ['field' => "document.files.$lang.filename", 'message' => 'filename is required'];
      }

      if (empty($file['content_base64'])) {
        $errors[] = ['field' => "document.files.$lang.content_base64", 'message' => 'content_base64 is required'];
        continue;
      }

      $decoded = base64_decode((string) $file['content_base64'], strict: TRUE);
      if ($decoded === FALSE) {
        $errors[] = ['field' => "document.files.$lang.content_base64", 'message' => 'Invalid base64 encoding'];
        continue;
      }

      if (!str_starts_with($decoded, '%PDF-')) {
        $errors[] = ['field' => "document.files.$lang.content_base64", 'message' => 'File does not appear to be a valid PDF (missing %PDF- header)'];
        continue;
      }

      $maxBytes = $this->maxPdfSizeMb * 1024 * 1024;
      if (strlen($decoded) > $maxBytes) {
        $errors[] = ['field' => "document.files.$lang.content_base64", 'message' => sprintf('PDF exceeds %dMB limit', $this->maxPdfSizeMb)];
      }
    }

    return $errors;
  }

  private function validateContent(IngestPayload $payload): array {
    $errors = [];

    if ($payload->mode === IngestPayload::MODE_TAGS_ONLY) {
      return $errors;
    }

    foreach (IngestPayload::SUPPORTED_LANGS as $lang) {
      $content = $payload->content[$lang] ?? NULL;

      if ($content === NULL) {
        $errors[] = ['field' => "content.$lang", 'message' => "Content for language '$lang' is required"];
        continue;
      }

      if (empty($content->slug)) {
        $errors[] = ['field' => "content.$lang.slug", 'message' => 'slug is required'];
      }

      if (empty($content->title)) {
        $errors[] = ['field' => "content.$lang.title", 'message' => 'title is required'];
      }
      elseif (mb_strlen($content->title) > self::MAX_TITLE_LENGTH) {
        $errors[] = ['field' => "content.$lang.title", 'message' => 'title exceeds 255 characters'];
      }

      if (empty($content->subtitle)) {
        $errors[] = ['field' => "content.$lang.subtitle", 'message' => 'subtitle is required'];
      }
      elseif (mb_strlen($content->subtitle) > self::MAX_SUBTITLE_LENGTH) {
        $errors[] = ['field' => "content.$lang.subtitle", 'message' => 'subtitle exceeds 255 characters'];
      }

      if (empty($content->bodySummary)) {
        $errors[] = ['field' => "content.$lang.body_summary", 'message' => 'body_summary is required'];
      }

      if (empty($content->body)) {
        $errors[] = ['field' => "content.$lang.body", 'message' => 'body is required'];
      }
      else {
        $sanitized = Xss::filterAdmin($content->body);
        $original_len = strlen($content->body);
        if ($original_len > 0) {
          $removed = ($original_len - strlen($sanitized)) / $original_len;
          if ($removed > 0.05) {
            $errors[] = ['field' => "content.$lang.body", 'message' => 'body HTML contains potentially unsafe content (>5% removed by sanitizer)'];
          }
        }
      }

      if ($content->schema !== NULL) {
        if (empty($content->schema['type']) || !in_array($content->schema['type'], self::SCHEMA_TYPES, TRUE)) {
          $errors[] = ['field' => "content.$lang.schema.type", 'message' => sprintf('schema.type must be one of: %s', implode(', ', self::SCHEMA_TYPES))];
        }

        if (empty($content->schema['json_ld']) || !is_array($content->schema['json_ld'])) {
          $errors[] = ['field' => "content.$lang.schema.json_ld", 'message' => 'schema.json_ld must be a JSON object'];
        }
      }
    }

    return $errors;
  }

}
