<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\sityos_content_api\ValueObject\TagPayload;
use Psr\Log\LoggerInterface;

final class TagManager {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Ensures new tags exist in EN and returns a map of EN name → TID for all tags.
   *
   * @return array<string, int> Map of EN tag name → taxonomy term ID.
   */
  public function ensureTags(TagPayload $payload): array {
    $storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $tidMap = [];

    foreach ($payload->new as $tagSet) {
      $enName = (string) $tagSet['en'];
      $existing = $storage->loadByProperties(['name' => $enName, 'vid' => 'tags', 'langcode' => 'en']);

      if (!empty($existing)) {
        $term = reset($existing);
        $tid = (int) $term->id();
        $tidMap[$enName] = $tid;
        $this->logger->info('Sityos API: tag "@name" already exists (TID @tid)', ['@name' => $enName, '@tid' => $tid]);
      }
      else {
        $term = $storage->create([
          'name' => $enName,
          'vid' => 'tags',
          'langcode' => 'en',
        ]);
        $term->save();
        $tid = (int) $term->id();
        $tidMap[$enName] = $tid;
        $this->logger->info('Sityos API: created tag "@name" (TID @tid)', ['@name' => $enName, '@tid' => $tid]);
      }
    }

    foreach ($payload->existing as $enName) {
      if (isset($tidMap[$enName])) {
        continue;
      }
      $found = $storage->loadByProperties(['name' => $enName, 'vid' => 'tags']);
      if (!empty($found)) {
        $term = reset($found);
        $tidMap[$enName] = (int) $term->id();
      }
    }

    return $tidMap;
  }

  /**
   * Adds ES and CA translations to the new tags.
   *
   * @param array<string, int> $tidMap EN name → TID.
   */
  public function ensureTagTranslations(array $tidMap, TagPayload $payload): void {
    $storage = $this->entityTypeManager->getStorage('taxonomy_term');

    foreach ($payload->new as $tagSet) {
      $enName = (string) $tagSet['en'];
      $tid = $tidMap[$enName] ?? NULL;

      if ($tid === NULL) {
        continue;
      }

      $term = $storage->load($tid);
      if ($term === NULL) {
        continue;
      }

      foreach (['es', 'ca'] as $lang) {
        $translation = (string) ($tagSet[$lang] ?? $enName);

        if ($term->hasTranslation($lang)) {
          $this->logger->info('Sityos API: tag TID @tid already has @lang translation', ['@tid' => $tid, '@lang' => $lang]);
          continue;
        }

        $term->addTranslation($lang, ['name' => $translation]);
        $this->logger->info('Sityos API: added @lang translation "@name" to TID @tid', ['@lang' => $lang, '@name' => $translation, '@tid' => $tid]);
      }

      $term->save();
    }
  }

}
