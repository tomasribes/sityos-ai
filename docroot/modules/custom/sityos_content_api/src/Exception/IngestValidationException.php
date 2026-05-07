<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\Exception;

final class IngestValidationException extends \RuntimeException {

  /**
   * @param array<array{field: string, message: string}> $errors
   */
  public function __construct(
    public readonly array $errors,
  ) {
    parent::__construct('Input validation failed');
  }

}
