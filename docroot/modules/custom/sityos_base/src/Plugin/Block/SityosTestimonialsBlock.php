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
        // [DATO PLACEHOLDER] — replace author/role/company/avatar_url with real data before launch.
        'items' => [
          [
            'quote' => $this->t('Sityos cut our onboarding automation from 3 days to 4 hours. The use cases are incredibly practical.'),
            'author' => $this->t('[Name]'),
            'role' => $this->t('[Role]'),
            'company' => $this->t('[Company]'),
            'avatar_url' => '',
          ],
          [
            'quote' => $this->t('Finally, a resource that explains AI automation without the hype. Pure actionable content.'),
            'author' => $this->t('[Name]'),
            'role' => $this->t('[Role]'),
            'company' => $this->t('[Company]'),
            'avatar_url' => '',
          ],
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
