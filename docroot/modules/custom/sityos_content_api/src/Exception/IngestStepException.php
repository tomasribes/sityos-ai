<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\Exception;

final class IngestStepException extends \RuntimeException {

  /**
   * @param string[] $completedSteps
   */
  public function __construct(
    string $message,
    public readonly array $completedSteps,
    public readonly string $errorId,
    public readonly string $failedStep = '',
  ) {
    parent::__construct($message);
  }

}
