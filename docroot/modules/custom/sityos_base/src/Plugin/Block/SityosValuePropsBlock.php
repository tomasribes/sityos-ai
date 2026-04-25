<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * @Block(
 *   id = "sityos_value_props",
 *   admin_label = @Translation("Sityos: Value Propositions"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosValuePropsBlock extends BlockBase {

  public function build(): array {
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    return [
      '#type' => 'component',
      '#component' => 'sityos_automate_olivero:feature-grid',
      '#props' => [
        'heading' => $this->getHeading($lang),
        'columns' => 3,
        'variant' => 'cards',
        'items' => $this->getPillars($lang),
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'max-age' => Cache::PERMANENT,
      ],
    ];
  }

  private function getHeading(string $lang): string {
    return match ($lang) {
      'es' => 'Por qué los equipos eligen Sityos Automate',
      'ca' => 'Per què els equips trien Sityos Automate',
      default => 'Why teams choose Sityos Automate',
    };
  }

  private function getPillars(string $lang): array {
    return match ($lang) {
      'es' => [
        [
          'icon' => '⚡',
          'title' => 'Flujos de Trabajo IA Prácticos',
          'description' => 'Guías paso a paso para entornos reales. Sin teoría — playbooks de automatización accionables que puedes implementar hoy.',
          'url' => '/es/tutorials',
        ],
        [
          'icon' => '🎯',
          'title' => 'Casos de Uso del Mundo Real',
          'description' => 'Aprende de implementaciones documentadas. Descubre cómo los equipos usan IA para reducir costes y acelerar resultados.',
          'url' => '/es/use-cases',
        ],
        [
          'icon' => '📚',
          'title' => 'Tutoriales con Criterio Experto',
          'description' => 'De básico a avanzado. Stack completo de automatización — desde prompt engineering hasta orquestación multi-agente.',
          'url' => '/es/tutorials',
        ],
      ],
      'ca' => [
        [
          'icon' => '⚡',
          'title' => 'Fluxos de Treball IA Pràctics',
          'description' => "Guies pas a pas per a entorns reals. Sense teoria — playbooks d'automatització accionables que pots implementar avui.",
          'url' => '/ca/tutorials',
        ],
        [
          'icon' => '🎯',
          'title' => "Casos d'Ús del Món Real",
          'description' => "Aprèn d'implementacions documentades. Descobreix com els equips fan servir la IA per reduir costos i accelerar resultats.",
          'url' => '/ca/use-cases',
        ],
        [
          'icon' => '📚',
          'title' => 'Tutorials amb Criteri Expert',
          'description' => "Des de bàsic fins a avançat. Stack complet d'automatització — des del prompt engineering fins a l'orquestació multi-agent.",
          'url' => '/ca/tutorials',
        ],
      ],
      default => [
        [
          'icon' => '⚡',
          'title' => 'Practical AI Workflows',
          'description' => 'Step-by-step guides built for real work environments. No theory — just actionable automation playbooks you can implement today.',
          'url' => '/tutorials',
        ],
        [
          'icon' => '🎯',
          'title' => 'Real-World Use Cases',
          'description' => 'Learn from documented implementations across industries. See exactly how teams are using AI to cut costs and accelerate delivery.',
          'url' => '/use-cases',
        ],
        [
          'icon' => '📚',
          'title' => 'Expert-Curated Tutorials',
          'description' => 'From beginner to advanced. Our tutorials cover the full automation stack — from prompt engineering to multi-agent orchestration.',
          'url' => '/tutorials',
        ],
      ],
    };
  }

  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages:language_interface']);
  }

}
