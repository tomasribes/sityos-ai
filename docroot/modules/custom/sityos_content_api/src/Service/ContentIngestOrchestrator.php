<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\Service;

use Drupal\Core\Database\Connection;
use Drupal\sityos_content_api\Exception\IngestStepException;
use Drupal\sityos_content_api\ValueObject\IngestPayload;
use Psr\Log\LoggerInterface;

final class ContentIngestOrchestrator {

  public function __construct(
    private readonly TagManager $tagManager,
    private readonly MediaDocumentManager $mediaDocumentManager,
    private readonly NodeContentCreator $nodeContentCreator,
    private readonly Connection $database,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Orchestrates all 6 creation steps inside a single DB transaction.
   *
   * @return array<string, mixed> Result data for the API response.
   *
   * @throws \Drupal\sityos_content_api\Exception\IngestStepException On any failure.
   */
  public function ingest(IngestPayload $payload): array {
    $transaction = $this->database->startTransaction();
    $completedSteps = [];
    $failedStep = '';

    try {
      // Step 1: Create new tags in EN.
      $tidMap = $this->tagManager->ensureTags($payload->tags);
      $completedSteps[] = 'tags';

      // Step 2: Add ES and CA translations to new tags.
      $this->tagManager->ensureTagTranslations($tidMap, $payload->tags);
      $completedSteps[] = 'tag_translations';

      $mediaId = NULL;

      if ($payload->document !== NULL) {
        // Step 3 & 4: Create media entity (EN) + add ES/CA translations.
        $mediaId = $this->mediaDocumentManager->createMediaWithTranslations($payload->document);
        $completedSteps[] = 'document_en';
        $completedSteps[] = 'document_translations';
      }

      if ($payload->mode === IngestPayload::MODE_TAGS_ONLY) {
        return [
          'steps_completed' => $completedSteps,
          'tags_created' => $this->getNewTagNames($payload),
        ];
      }

      // Step 5: Create node in EN.
      $node = $this->nodeContentCreator->createNode($payload, $tidMap, $mediaId);
      $completedSteps[] = 'node_en';

      // Step 6: Add ES and CA translations to the node.
      foreach (['es', 'ca'] as $lang) {
        $this->nodeContentCreator->addTranslation($node, $payload, $lang);
      }
      $completedSteps[] = 'node_translations';

      // Set all path aliases after all node saves to prevent Pathauto race.
      $this->nodeContentCreator->setAllPathAliases($node, $payload);

      $this->logger->info('Sityos API: ingest completed for node @id', ['@id' => $node->id()]);

      return [
        'node_id' => (int) $node->id(),
        'node_uuid' => $node->uuid(),
        'urls' => $this->buildUrlMap((int) $node->id(), $payload),
        'tags_created' => $this->getNewTagNames($payload),
        'document_media_id' => $mediaId,
        'steps_completed' => $completedSteps,
      ];
    }
    catch (\Exception $e) {
      $transaction->rollBack();

      $errorId = 'sityos_content_api.' . date('Ymd.His') . '.' . substr(md5(uniqid()), 0, 6);
      $this->logger->error('Sityos API: ingest failed at step "@step" — @msg (error_id: @id)', [
        '@step' => $failedStep,
        '@msg' => $e->getMessage(),
        '@id' => $errorId,
      ]);

      throw new IngestStepException(
        message: $e->getMessage(),
        completedSteps: $completedSteps,
        errorId: $errorId,
        failedStep: $failedStep,
      );
    }
  }

  /**
   * @return string[]
   */
  private function getNewTagNames(IngestPayload $payload): array {
    return array_map(fn($tag) => (string) $tag['en'], $payload->tags->new);
  }

  /**
   * @return array<string, string>
   */
  private function buildUrlMap(int $nodeId, IngestPayload $payload): array {
    $urls = [];
    foreach (IngestPayload::SUPPORTED_LANGS as $lang) {
      $content = $payload->content[$lang] ?? NULL;
      if ($content !== NULL) {
        $prefixes = [
          IngestPayload::TYPE_TUTORIAL => ['en' => '/tutorials/', 'es' => '/tutoriales/', 'ca' => '/tutorials/'],
          IngestPayload::TYPE_USE_CASE => ['en' => '/use-cases/', 'es' => '/casos-de-uso/', 'ca' => '/casos-uso/'],
        ];
        $prefix = $prefixes[$payload->contentType][$lang] ?? '/node/';
        $urls[$lang] = $prefix . $content->slug;
      }
    }
    return $urls;
  }

}
