<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * @Block(
 *   id = "sityos_social_proof",
 *   admin_label = @Translation("Sityos: Social Proof Bar"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosSocialProofBlock extends BlockBase {

  public function build(): array {
    return [
      '#type' => 'component',
      '#component' => 'sityos_automate_olivero:social-proof',
      '#props' => [
        'intro_text' => $this->t('Trusted by professionals at'),
        'logos' => [],
        'metrics' => [
          ['value' => '50+', 'label' => $this->t('Tutorials Published')],
          ['value' => '10K+', 'label' => $this->t('Monthly Readers')],
          ['value' => '30+', 'label' => $this->t('Use Cases')],
        ],
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
