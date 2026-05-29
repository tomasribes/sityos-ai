<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\Service;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\sityos_content_api\ValueObject\ContentPayload;
use Drupal\sityos_content_api\ValueObject\IngestPayload;
use Psr\Log\LoggerInterface;

final class NodeContentCreator {

  private const PATH_PREFIXES = [
    IngestPayload::TYPE_TUTORIAL => ['en' => '/tutorials/', 'es' => '/tutoriales/', 'ca' => '/tutorials/'],
    IngestPayload::TYPE_USE_CASE => ['en' => '/use-cases/', 'es' => '/casos-de-uso/', 'ca' => '/casos-uso/'],
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Creates the node in the default language (EN) with all fields.
   *
   * @param array<string, int> $tagTids EN tag name → TID map.
   */
  public function createNode(IngestPayload $payload, array $tagTids, ?int $mediaId): NodeInterface {
    $content = $payload->content['en'];
    $nodeData = $this->buildNodeData($payload->contentType, $content, $tagTids, $mediaId, 'en');

    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->entityTypeManager->getStorage('node')->create($nodeData);
    $node->save();

    $this->logger->info('Sityos API: created @type node @id (@lang)', [
      '@type' => $payload->contentType,
      '@id' => $node->id(),
      '@lang' => 'en',
    ]);

    return $node;
  }

  /**
   * Adds an ES or CA translation to an existing node.
   */
  /**
   * Sets path aliases for all languages after all node saves are complete.
   * Called once from the orchestrator to avoid Pathauto override races.
   */
  public function setAllPathAliases(NodeInterface $node, IngestPayload $payload): void {
    $nodeId = (int) $node->id();
    foreach (IngestPayload::SUPPORTED_LANGS as $lang) {
      $content = $payload->content[$lang] ?? NULL;
      if ($content !== NULL) {
        $this->setPathAlias($nodeId, $content->slug, $payload->contentType, $lang);
      }
    }
  }

  public function addTranslation(NodeInterface $node, IngestPayload $payload, string $langcode): void {
    $content = $payload->content[$langcode] ?? NULL;
    if ($content === NULL) {
      throw new \RuntimeException("Missing content for language '$langcode'");
    }

    $translationData = [
      'title' => $content->title,
      'field_subtitle' => $content->subtitle,
      'body' => [
        'value' => Xss::filterAdmin($content->body),
        'summary' => $content->bodySummary,
        'format' => 'full_html',
      ],
      'field_meta_tags' => $this->buildMetatagsField($content),
    ];

    $node->addTranslation($langcode, $translationData);
    $node->save();

    $this->logger->info('Sityos API: added @lang translation to node @id', ['@lang' => $langcode, '@id' => $node->id()]);
  }

  /**
   * @param array<string, int> $tagTids
   */
  private function buildNodeData(string $contentType, ContentPayload $content, array $tagTids, ?int $mediaId, string $langcode): array {
    $tagReferences = array_map(fn($tid) => ['target_id' => $tid], array_values($tagTids));

    $data = [
      'type' => $contentType,
      'langcode' => $langcode,
      'status' => 0,
      'uid' => 1,
      'title' => $content->title,
      'field_subtitle' => $content->subtitle,
      'body' => [
        'value' => Xss::filterAdmin($content->body),
        'summary' => $content->bodySummary,
        'format' => 'full_html',
      ],
      'field_tags' => $tagReferences,
      'field_meta_tags' => $this->buildMetatagsField($content),
    ];

    if ($contentType === IngestPayload::TYPE_TUTORIAL && $mediaId !== NULL) {
      $data['field_media_document'] = ['target_id' => $mediaId];
    }

    return $data;
  }

  private function buildMetatagsField(ContentPayload $content): array {
    $schema = $content->schema;

    if ($schema === NULL || empty($schema['type']) || empty($schema['json_ld'])) {
      return [['value' => '{}']];
    }

    $type = strtolower((string) $schema['type']);
    $jsonLd = (array) $schema['json_ld'];
    $metaTags = [];

    match ($type) {
      'howto' => $metaTags = $this->mapHowToSchema($jsonLd),
      'article', 'techarticle' => $metaTags = $this->mapArticleSchema($jsonLd, $schema['type']),
      default => NULL,
    };

    return [['value' => json_encode($metaTags)]];
  }

  /**
   * Maps JSON-LD HowTo object to schema_metatag flat storage format.
   */
  private function mapHowToSchema(array $jsonLd): array {
    $metatags = [
      'schema_how_to_type' => 'HowTo',
    ];

    if (!empty($jsonLd['name'])) {
      $metatags['schema_how_to_name'] = (string) $jsonLd['name'];
      $metatags['schema_how_to_id'] = '[node:url]';
    }

    if (!empty($jsonLd['description'])) {
      $metatags['schema_how_to_description'] = (string) $jsonLd['description'];
    }

    if (!empty($jsonLd['totalTime'])) {
      $metatags['schema_how_to_total_time'] = (string) $jsonLd['totalTime'];
    }

    if (!empty($jsonLd['tool']) && is_array($jsonLd['tool'])) {
      $toolNames = array_map(fn($t) => (string) ($t['name'] ?? $t), $jsonLd['tool']);
      $metatags['schema_how_to_tool'] = implode(', ', $toolNames);
    }

    if (!empty($jsonLd['step']) && is_array($jsonLd['step'])) {
      $stepNames = array_map(fn($s) => (string) ($s['name'] ?? ''), $jsonLd['step']);
      $stepTexts = array_map(fn($s) => (string) ($s['text'] ?? $s['name'] ?? ''), $jsonLd['step']);
      $metatags['schema_how_to_step'] = serialize([
        '@type' => 'HowToStep',
        'pivot' => '1',
        'name' => implode(', ', $stepNames),
        'text' => implode(', ', $stepTexts),
      ]);
    }

    if (!empty($jsonLd['estimatedCost']) && is_array($jsonLd['estimatedCost'])) {
      $cost = $jsonLd['estimatedCost'];
      $metatags['schema_how_to_estimated_cost'] = serialize([
        '@type' => $cost['@type'] ?? 'MonetaryAmount',
        'currency' => $cost['currency'] ?? 'USD',
        'value' => ['@type' => 'QuantitativeValue', 'value' => (string) ($cost['value'] ?? '')],
      ]);
    }

    return $metatags;
  }

  /**
   * Maps JSON-LD Article/TechArticle object to schema_metatag flat storage format.
   */
  private function mapArticleSchema(array $jsonLd, string $type): array {
    $metatags = [
      'schema_article_type' => $type,
    ];

    if (!empty($jsonLd['name'])) {
      $metatags['schema_article_name'] = (string) $jsonLd['name'];
      $metatags['schema_article_id'] = '[node:url]';
    }

    if (!empty($jsonLd['description'])) {
      $metatags['schema_article_description'] = (string) $jsonLd['description'];
    }

    if (!empty($jsonLd['datePublished'])) {
      $metatags['schema_article_date_published'] = (string) $jsonLd['datePublished'];
    }

    if (!empty($jsonLd['author'])) {
      $author = $jsonLd['author'];
      $metatags['schema_article_author'] = serialize([
        '@type' => $author['@type'] ?? 'Person',
        'name' => $author['name'] ?? '',
      ]);
    }

    if (!empty($jsonLd['publisher'])) {
      $publisher = $jsonLd['publisher'];
      $metatags['schema_article_publisher'] = serialize([
        '@type' => $publisher['@type'] ?? 'Organization',
        'name' => $publisher['name'] ?? '',
        'url' => $publisher['url'] ?? '',
      ]);
    }

    return $metatags;
  }

  private function setPathAlias(int $nodeId, string $slug, string $contentType, string $langcode): void {
    $prefix = self::PATH_PREFIXES[$contentType][$langcode] ?? '/content/';
    $alias = $prefix . $slug;
    $path = '/node/' . $nodeId;

    $aliasStorage = $this->entityTypeManager->getStorage('path_alias');

    // Delete any existing alias for this path + langcode to avoid duplicates.
    $existing = $aliasStorage->loadByProperties([
      'path' => $path,
      'langcode' => $langcode,
    ]);
    foreach ($existing as $existingAlias) {
      $existingAlias->delete();
    }

    $aliasEntity = $aliasStorage->create([
      'path' => $path,
      'alias' => $alias,
      'langcode' => $langcode,
    ]);
    $aliasEntity->save();

    $this->logger->info('Sityos API: set alias @alias for node @id (@lang)', [
      '@alias' => $alias,
      '@id' => $nodeId,
      '@lang' => $langcode,
    ]);
  }

}
