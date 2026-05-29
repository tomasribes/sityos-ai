<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * @Block(
 *   id = "sityos_testimonials_home",
 *   admin_label = @Translation("Sityos: Testimonials"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosTestimonialsBlock extends BlockBase {

  public function build(): array {
    return [
      '#type' => 'component',
      '#component' => 'sityos_automate_olivero:testimonials',
      '#props' => [
        'heading' => $this->t('What Professionals Are Saying'),
        'variant' => 'grid',
        // Real testimonials pending — block renders empty until filled with verified data.
        'items' => [],
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
