<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * @Block(
 *   id = "sityos_tutorials_cta",
 *   admin_label = @Translation("Sityos: Tutorials Section CTA"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosTutorialsCtaBlock extends BlockBase {

  public function build(): array {
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    [$label, $url] = match ($lang) {
      'es' => ['Ver Todos los Tutoriales →', '/es/tutorials'],
      'ca' => ['Veure Tots els Tutorials →', '/ca/tutorials'],
      default => ['Browse All Tutorials →', '/tutorials'],
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
