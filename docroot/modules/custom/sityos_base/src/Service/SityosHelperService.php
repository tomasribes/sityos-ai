<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Shared utility service for the Sityos Automate platform.
 */
final class SityosHelperService {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly \Drupal\Component\Datetime\TimeInterface $time,
  ) {}

  /**
   * Returns the list of active language codes on the site.
   *
   * @return string[]
   *   Indexed array of language codes, e.g. ['en', 'es', 'ca'].
   */
  public function getLanguages(): array {
    return array_keys(
      $this->languageManager->getLanguages(LanguageInterface::STATE_CONFIGURABLE)
    );
  }

  /**
   * Returns a site configuration value by key.
   *
   * @param string $key
   *   Dot-notation key, e.g. 'page.front'.
   *
   * @return mixed
   *   The configuration value, or NULL if not set.
   */
  public function getSiteConfig(string $key): mixed {
    return $this->configFactory->get('system.site')->get($key);
  }

  /**
   * Formats a Unix timestamp as a human-readable date string.
   *
   * @param int|null $timestamp
   *   Unix timestamp. Defaults to current time if NULL.
   * @param string $format
   *   PHP date format string. Defaults to 'Y-m-d H:i'.
   * @param string $timezone
   *   Timezone string. Defaults to UTC.
   *
   * @return string
   *   Formatted date string.
   */
  public function formatDate(?int $timestamp = NULL, string $format = 'Y-m-d H:i', string $timezone = 'UTC'): string {
    $timestamp ??= $this->time->getCurrentTime();
    $date = DrupalDateTime::createFromTimestamp($timestamp, new \DateTimeZone($timezone));
    return $date->format($format);
  }

}
