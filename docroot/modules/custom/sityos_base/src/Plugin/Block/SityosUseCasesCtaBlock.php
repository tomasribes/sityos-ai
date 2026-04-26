<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Url;

/**
 * @Block(
 *   id = "sityos_use_cases_cta",
 *   admin_label = @Translation("Sityos: Use Cases Section CTA"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosUseCasesCtaBlock extends BlockBase {

  public function build(): array {
    $language = \Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_URL);
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['sao-section-cta']],
      'link' => [
        '#type' => 'link',
        '#title' => $this->t('View All Use Cases →'),
        '#url' => Url::fromUserInput('/use-cases', ['language' => $language]),
        '#attributes' => ['class' => ['sao-btn', 'sao-btn--secondary']],
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'max-age' => Cache::PERMANENT,
      ],
    ];
  }

  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages:language_interface']);
  }

}
