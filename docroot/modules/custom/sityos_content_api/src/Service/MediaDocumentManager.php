<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\FileRepositoryInterface;
use Drupal\sityos_content_api\ValueObject\DocumentPayload;
use Psr\Log\LoggerInterface;

final class MediaDocumentManager {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly FileSystemInterface $fileSystem,
    private readonly FileRepositoryInterface $fileRepository,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Creates one media entity (bundle: document) with EN/ES/CA translations.
   *
   * Replicates the manual steps 3 and 4:
   *   - Step 3: Upload the PDF in English → creates the EN media entity.
   *   - Step 4: Add ES and CA translations to the same media entity.
   *
   * @return int The media entity ID (same ID referenced by all node translations).
   */
  public function createMediaWithTranslations(DocumentPayload $payload): int {
    $dir = 'public://documents/' . date('Y-m');

    if (!$this->fileSystem->prepareDirectory($dir, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      throw new \RuntimeException('Cannot prepare document directory: ' . $dir);
    }

    // Step 3: Create media entity in EN.
    $enFile = $this->saveFile($payload, 'en', $dir);
    $media = $this->entityTypeManager->getStorage('media')->create([
      'bundle' => 'document',
      'name' => $payload->name . ' (EN)',
      'langcode' => 'en',
      'status' => 1,
      'uid' => 1,
      'field_media_document' => ['target_id' => $enFile->id()],
    ]);
    $media->save();

    $mediaId = (int) $media->id();
    $this->logger->info('Sityos API: created document media entity @id for "@name" (EN)', ['@id' => $mediaId, '@name' => $payload->name]);

    // Step 4: Add ES and CA translations to the same media entity.
    foreach (['es', 'ca'] as $lang) {
      $langFile = $this->saveFile($payload, $lang, $dir);
      $media->addTranslation($lang, [
        'name' => $payload->name . ' (' . strtoupper($lang) . ')',
        'status' => 1,
        'field_media_document' => ['target_id' => $langFile->id()],
      ]);
      $this->logger->info('Sityos API: added @lang translation to media @id', ['@lang' => $lang, '@id' => $mediaId]);
    }

    $media->save();

    return $mediaId;
  }

  private function saveFile(DocumentPayload $payload, string $lang, string $dir): \Drupal\file\FileInterface {
    $fileData = $payload->files[$lang] ?? NULL;

    if ($fileData === NULL) {
      throw new \RuntimeException("Missing PDF file data for language '$lang'");
    }

    $decoded = base64_decode((string) $fileData['content_base64'], strict: TRUE);
    if ($decoded === FALSE) {
      throw new \RuntimeException("Invalid base64 content for PDF '$lang'");
    }

    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', (string) ($fileData['filename'] ?? $payload->name . "_$lang.pdf"));
    $uri = $dir . '/' . $filename;

    $file = $this->fileRepository->writeData($decoded, $uri, FileSystemInterface::EXISTS_RENAME);
    $this->logger->info('Sityos API: saved PDF file @uri (FID @fid)', ['@uri' => $file->getFileUri(), '@fid' => $file->id()]);

    return $file;
  }

}
