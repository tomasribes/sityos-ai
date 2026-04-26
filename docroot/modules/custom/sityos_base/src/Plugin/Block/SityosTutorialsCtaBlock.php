<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Url;

/**
 * @Block(
 *   id = "sityos_tutorials_cta",
 *   admin_label = @Translation("Sityos: Tutorials Section CTA"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosTutorialsCtaBlock extends BlockBase {

  public function build(): array {
    $language = \Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_URL);
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['sao-section-cta']],
      'link' => [
        '#type' => 'link',
        '#title' => $this->t('Browse All Tutorials →'),
        '#url' => Url::fromUserInput('/tutorials', ['language' => $language]),
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
