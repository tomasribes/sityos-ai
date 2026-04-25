<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * @Block(
 *   id = "sityos_cta_central",
 *   admin_label = @Translation("Sityos: CTA Central"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosCtaCentralBlock extends BlockBase {

  public function build(): array {
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    return [
      '#type' => 'component',
      '#component' => 'sityos_automate_olivero:cta-central',
      '#props' => $this->getProps($lang),
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'max-age' => Cache::PERMANENT,
      ],
    ];
  }

  private function getProps(string $lang): array {
    return match ($lang) {
      'es' => [
        'headline' => '¿Listo para Automatizar tu Flujo de Trabajo?',
        'body' => 'Únete a profesionales que usan Sityos Automate para trabajar de forma más inteligente. Recibe tutoriales semanales, casos de uso e insights de automatización — gratis.',
        'cta_primary_label' => 'Suscríbete Gratis →',
        'cta_primary_url' => '/es/subscribe',
        'cta_secondary_label' => 'Explorar Contenido Primero',
        'cta_secondary_url' => '/es/tutorials',
        'microcopy' => 'Sin spam. Cancela cuando quieras.',
      ],
      'ca' => [
        'headline' => 'Preparat per Automatitzar el teu Flux de Treball?',
        'body' => "Uneix-te a professionals que utilitzen Sityos Automate per treballar de manera més intel·ligent. Rep tutorials setmanals, casos d'ús i insights d'automatització — gratis.",
        'cta_primary_label' => 'Subscriu-te Gratis →',
        'cta_primary_url' => '/ca/subscribe',
        'cta_secondary_label' => 'Explorar Contingut Primer',
        'cta_secondary_url' => '/ca/tutorials',
        'microcopy' => "Sense spam. Cancel·la quan vulguis.",
      ],
      default => [
        'headline' => 'Ready to Automate Your Workflow?',
        'body' => 'Join professionals using Sityos Automate to work smarter. Get weekly tutorials, use cases, and automation insights — free.',
        'cta_primary_label' => 'Subscribe Free →',
        'cta_primary_url' => '/subscribe',
        'cta_secondary_label' => 'Explore Content First',
        'cta_secondary_url' => '/tutorials',
        'microcopy' => 'No spam. Unsubscribe anytime.',
      ],
    };
  }

  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages:language_interface']);
  }

}
