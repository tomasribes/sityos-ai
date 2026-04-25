<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * @Block(
 *   id = "sityos_use_cases_cta",
 *   admin_label = @Translation("Sityos: Use Cases Section CTA"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosUseCasesCtaBlock extends BlockBase {

  public function build(): array {
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    [$label, $url] = match ($lang) {
      'es' => ['Ver Todos los Casos de Uso →', '/es/use-cases'],
      'ca' => ["Veure Tots els Casos d'Ús →", '/ca/use-cases'],
      default => ['View All Use Cases →', '/use-cases'],
    };
    return [
      '#markup' => '<div class="sao-section-cta"><a href="' . $url . '" class="sao-btn sao-btn--secondary">' . $label . '</a></div>',
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
