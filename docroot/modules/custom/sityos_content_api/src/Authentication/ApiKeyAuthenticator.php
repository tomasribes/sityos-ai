<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\Authentication;

use Drupal\Core\Database\Connection;
use Drupal\key\KeyRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class ApiKeyAuthenticator {

  private const HEADER = 'X-Sityos-Api-Key';
  private const RATE_LIMIT_WINDOW = 60;

  public function __construct(
    private readonly KeyRepositoryInterface $keyRepository,
    private readonly Connection $database,
    private readonly LoggerInterface $logger,
    private readonly string $keyId = 'sityos_content_api_key',
    private readonly int $rateLimitRpm = 60,
  ) {}

  public function authenticate(Request $request): void {
    $ip = $request->getClientIp() ?? 'unknown';
    $provided = $request->headers->get(self::HEADER);

    if (empty($provided)) {
      $this->logger->warning('Sityos Content API: missing API key from IP @ip', ['@ip' => $ip]);
      throw new AccessDeniedHttpException('Missing ' . self::HEADER . ' header');
    }

    $expected = $this->getExpectedKey();
    if (!hash_equals($expected, $provided)) {
      $this->logger->warning('Sityos Content API: invalid API key from IP @ip', ['@ip' => $ip]);
      throw new AccessDeniedHttpException('Invalid API key');
    }

    if ($this->isRateLimited($ip)) {
      throw new TooManyRequestsHttpException(self::RATE_LIMIT_WINDOW, 'Rate limit exceeded. Max ' . $this->rateLimitRpm . ' requests per minute.');
    }

    $this->recordRequest($ip);
  }

  private function getExpectedKey(): string {
    $key = $this->keyRepository->getKey($this->keyId);
    if ($key === NULL) {
      $this->logger->error('Sityos Content API: key entity "@id" not found', ['@id' => $this->keyId]);
      throw new AccessDeniedHttpException('API key configuration error');
    }

    return (string) $key->getKeyValue();
  }

  private function isRateLimited(string $ip): bool {
    $windowStart = time() - self::RATE_LIMIT_WINDOW;

    // Clean up old records.
    $this->database->delete('sityos_api_rate_limit')
      ->condition('timestamp', $windowStart - 3600, '<')
      ->execute();

    $count = (int) $this->database->select('sityos_api_rate_limit', 'r')
      ->condition('r.ip', $ip)
      ->condition('r.timestamp', $windowStart, '>')
      ->countQuery()
      ->execute()
      ->fetchField();

    return $count >= $this->rateLimitRpm;
  }

  private function recordRequest(string $ip): void {
    $this->database->insert('sityos_api_rate_limit')
      ->fields(['ip' => $ip, 'timestamp' => time()])
      ->execute();
  }

}
