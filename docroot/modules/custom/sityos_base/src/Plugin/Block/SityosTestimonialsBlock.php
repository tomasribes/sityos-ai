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
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    return [
      '#type' => 'component',
      '#component' => 'sityos_automate_olivero:testimonials',
      '#props' => [
        'heading' => $this->getHeading($lang),
        'variant' => 'grid',
        'items' => $this->getItems($lang),
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'max-age' => Cache::PERMANENT,
      ],
    ];
  }

  private function getHeading(string $lang): string {
    return match ($lang) {
      'es' => 'Lo Que Dicen los Profesionales',
      'ca' => 'El Que Diuen els Professionals',
      default => 'What Professionals Are Saying',
    };
  }

  /**
   * Returns testimonial items.
   *
   * [DATO PLACEHOLDER] — replace author/role/company/avatar_url with real data before launch.
   */
  private function getItems(string $lang): array {
    return match ($lang) {
      'es' => [
        [
          'quote' => 'Sityos redujo nuestra automatización de onboarding de 3 días a 4 horas. Los casos de uso son increíblemente prácticos.',
          'author' => '[Nombre]',
          'role' => '[Cargo]',
          'company' => '[Empresa]',
          'avatar_url' => '',
        ],
        [
          'quote' => 'Por fin, un recurso que explica la automatización IA sin humo. Contenido puro y accionable.',
          'author' => '[Nombre]',
          'role' => '[Cargo]',
          'company' => '[Empresa]',
          'avatar_url' => '',
        ],
      ],
      'ca' => [
        [
          'quote' => "Sityos va reduir la nostra automatització d'onboarding de 3 dies a 4 hores. Els casos d'ús són increïblement pràctics.",
          'author' => '[Nom]',
          'role' => '[Càrrec]',
          'company' => '[Empresa]',
          'avatar_url' => '',
        ],
        [
          'quote' => "Per fi, un recurs que explica l'automatització IA sense fum. Contingut pur i accionable.",
          'author' => '[Nom]',
          'role' => '[Càrrec]',
          'company' => '[Empresa]',
          'avatar_url' => '',
        ],
      ],
      default => [
        [
          'quote' => 'Sityos cut our onboarding automation from 3 days to 4 hours. The use cases are incredibly practical.',
          'author' => '[Name]',
          'role' => '[Role]',
          'company' => '[Company]',
          'avatar_url' => '',
        ],
        [
          'quote' => 'Finally, a resource that explains AI automation without the hype. Pure actionable content.',
          'author' => '[Name]',
          'role' => '[Role]',
          'company' => '[Company]',
          'avatar_url' => '',
        ],
      ],
    };
  }

  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages:language_interface']);
  }

}
