<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

/**
 * Pexels API integration: search and import images to the Media Library.
 */
final class PexelsApiService {

  private const API_BASE = 'https://api.pexels.com/v1';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ClientInterface $httpClient,
    private readonly FileSystemInterface $fileSystem,
    private readonly LoggerInterface $logger,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  private function getApiKey(): string {
    return (string) ($this->configFactory->get('sityos_base.settings.pexels')->get('api_key') ?? '');
  }

  public function hasApiKey(): bool {
    return !empty($this->getApiKey());
  }

  /**
   * Tests the API key connectivity.
   *
   * @param string $api_key
   *   Override key (e.g. from the settings form before saving).
   */
  public function testConnection(string $api_key = ''): array {
    $key = $api_key ?: $this->getApiKey();
    if (empty($key)) {
      return ['ok' => FALSE, 'remaining' => 0, 'message' => 'API key not configured.'];
    }

    try {
      $response = $this->httpClient->request('GET', self::API_BASE . '/curated', [
        'headers' => ['Authorization' => $key],
        'query' => ['per_page' => 1],
        'timeout' => 10,
      ]);

      $remaining = (int) ($response->getHeaderLine('X-Ratelimit-Remaining') ?: 0);
      return ['ok' => TRUE, 'remaining' => $remaining, 'message' => ''];
    }
    catch (RequestException $e) {
      $code = $e->getResponse()?->getStatusCode() ?? 0;
      $message = match (TRUE) {
        $code === 401 => 'Invalid API key.',
        $code === 403 => 'Access forbidden — check your API key permissions.',
        $code >= 500 => 'Pexels API server error. Try again later.',
        default => 'Connection error: ' . $e->getMessage(),
      };
      return ['ok' => FALSE, 'remaining' => 0, 'message' => $message];
    }
  }

  /**
   * Searches Pexels for images.
   *
   * @return array{photos: array, total_results: int}
   */
  public function search(string $query, int $per_page = 20, int $page = 1): array {
    $key = $this->getApiKey();
    if (empty($key)) {
      return ['photos' => [], 'total_results' => 0];
    }

    try {
      $response = $this->httpClient->request('GET', self::API_BASE . '/search', [
        'headers' => ['Authorization' => $key],
        'query' => [
          'query' => $query,
          'per_page' => $per_page,
          'page' => $page,
          'locale' => 'en-US',
        ],
        'timeout' => 15,
      ]);

      $data = json_decode((string) $response->getBody(), TRUE);
      return [
        'photos' => $data['photos'] ?? [],
        'total_results' => $data['total_results'] ?? 0,
      ];
    }
    catch (RequestException $e) {
      $this->logger->error('Pexels search failed for "@query": @msg', [
        '@query' => $query,
        '@msg' => $e->getMessage(),
      ]);
      return ['photos' => [], 'total_results' => 0];
    }
  }

  /**
   * Checks if a Pexels photo is already in the Media Library by file URI.
   */
  public function isAlreadyImported(int $photo_id): bool {
    $result = $this->entityTypeManager->getStorage('file')
      ->getQuery()
      ->condition('uri', '%/pexels_' . $photo_id . '.jpg', 'LIKE')
      ->accessCheck(FALSE)
      ->range(0, 1)
      ->execute();
    return !empty($result);
  }

  /**
   * Downloads a Pexels photo and imports it as a File + Media entity.
   *
   * @return array{status: 'imported'|'skipped'|'error', media_id?: int, message?: string}
   */
  public function importPhoto(array $photo): array {
    $photo_id = (int) $photo['id'];
    $month = date('Y-m');
    $dir = "public://imports/ai-automation/{$month}";
    $uri = "{$dir}/pexels_{$photo_id}.jpg";

    if ($this->isAlreadyImported($photo_id)) {
      return ['status' => 'skipped'];
    }

    if (!$this->fileSystem->prepareDirectory($dir, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      $this->logger->error('Cannot prepare Pexels import directory @dir', ['@dir' => $dir]);
      return ['status' => 'error', 'message' => 'Cannot create directory.'];
    }

    $image_url = $photo['src']['large2x'] ?? $photo['src']['original'] ?? '';
    if (empty($image_url)) {
      return ['status' => 'error', 'message' => 'No image URL available.'];
    }

    try {
      $response = $this->httpClient->request('GET', $image_url, ['timeout' => 30]);
      $image_data = (string) $response->getBody();
    }
    catch (RequestException $e) {
      $this->logger->error('Failed to download Pexels photo @id: @msg', [
        '@id' => $photo_id,
        '@msg' => $e->getMessage(),
      ]);
      return ['status' => 'error', 'message' => 'Download failed.'];
    }

    if (file_put_contents($uri, $image_data) === FALSE) {
      $this->logger->error('Cannot write Pexels photo to @uri', ['@uri' => $uri]);
      return ['status' => 'error', 'message' => 'Cannot write file.'];
    }

    $alt = !empty($photo['alt']) ? substr((string) $photo['alt'], 0, 512) : 'Pexels photo ' . $photo_id;
    $title = !empty($photo['alt']) ? substr((string) $photo['alt'], 0, 255) : 'Pexels photo ' . $photo_id;

    $file = $this->entityTypeManager->getStorage('file')->create([
      'uri' => $uri,
      'status' => 1,
      'uid' => 1,
    ]);
    $file->save();

    $media = $this->entityTypeManager->getStorage('media')->create([
      'bundle' => 'image',
      'name' => $title,
      'langcode' => 'en',
      'status' => 1,
      'uid' => 1,
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => $alt,
      ],
    ]);
    $media->save();

    return ['status' => 'imported', 'media_id' => (int) $media->id()];
  }

}
