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
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    return [
      '#type' => 'component',
      '#component' => 'sityos_automate_olivero:social-proof',
      '#props' => [
        'intro_text' => $this->getIntroText($lang),
        'logos' => [],
        'metrics' => $this->getMetrics($lang),
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'max-age' => Cache::PERMANENT,
      ],
    ];
  }

  private function getIntroText(string $lang): string {
    return match ($lang) {
      'es' => 'Usado por profesionales en',
      'ca' => 'Utilitzat per professionals a',
      default => 'Trusted by professionals at',
    };
  }

  private function getMetrics(string $lang): array {
    return match ($lang) {
      'es' => [
        ['value' => '50+', 'label' => 'Tutoriales Publicados'],
        ['value' => '10K+', 'label' => 'Lectores Mensuales'],
        ['value' => '30+', 'label' => 'Casos de Uso'],
      ],
      'ca' => [
        ['value' => '50+', 'label' => 'Tutorials Publicats'],
        ['value' => '10K+', 'label' => 'Lectors Mensuals'],
        ['value' => '30+', 'label' => "Casos d'Ús"],
      ],
      default => [
        ['value' => '50+', 'label' => 'Tutorials Published'],
        ['value' => '10K+', 'label' => 'Monthly Readers'],
        ['value' => '30+', 'label' => 'Use Cases'],
      ],
    };
  }

  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages:language_interface']);
  }

}
