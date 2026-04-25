<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * @Block(
 *   id = "sityos_hero_home",
 *   admin_label = @Translation("Sityos: Hero Homepage"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosHeroHomeBlock extends BlockBase {

  public function build(): array {
    return [
      '#type' => 'component',
      '#component' => 'sityos_automate_olivero:hero',
      '#props' => [
        'eyebrow' => $this->t('AI Automation Platform'),
        'headline' => $this->t('Automate Smarter. <em>Work Less.</em> Achieve More.'),
        'subtitle' => $this->t('Sityos gives teams the practical AI workflows, tutorials, and tools they need to eliminate repetitive work and focus on what matters.'),
        'cta_primary_label' => $this->t('Explore Tutorials →'),
        'cta_primary_url' => '/tutorials',
        'cta_secondary_label' => $this->t('See Use Cases'),
        'cta_secondary_url' => '/use-cases',
        'microcopy' => $this->t('Join professionals already saving hours every week.'),
        'variant' => 'centered',
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:system.site'],
        'max-age' => Cache::PERMANENT,
      ],
    ];
  }

  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages:language_interface']);
  }

}
