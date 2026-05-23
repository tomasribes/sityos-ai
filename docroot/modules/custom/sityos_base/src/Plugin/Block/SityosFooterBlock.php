<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * @Block(
 *   id = "sityos_footer",
 *   admin_label = @Translation("Sityos: Site Footer"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosFooterBlock extends BlockBase {

  public function build(): array {
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $nav_columns = match($lang) {
      'es' => [
        [
          'heading' => 'Aprender',
          'links' => [
            ['label' => 'Tutoriales', 'url' => '/tutorials'],
            ['label' => 'Casos de Uso', 'url' => '/use-cases'],
            ['label' => 'Artículos', 'url' => '/articles'],
            ['label' => 'Etiquetas', 'url' => '/tags'],
          ],
        ],
        [
          'heading' => 'Empresa',
          'links' => [
            ['label' => 'Acerca de', 'url' => '/about'],
            ['label' => 'Suscribirse', 'url' => '/subscribe'],
          ],
        ],
        [
          'heading' => 'Legal',
          'links' => [
            ['label' => 'Política de Privacidad', 'url' => '/privacy-policy'],
            ['label' => 'Términos de Uso', 'url' => '/terms'],
          ],
        ],
      ],
      'ca' => [
        [
          'heading' => 'Aprendre',
          'links' => [
            ['label' => 'Tutorials', 'url' => '/tutorials'],
            ['label' => "Casos d'Ús", 'url' => '/use-cases'],
            ['label' => 'Articles', 'url' => '/articles'],
            ['label' => 'Etiquetes', 'url' => '/tags'],
          ],
        ],
        [
          'heading' => 'Empresa',
          'links' => [
            ['label' => 'Sobre nosaltres', 'url' => '/about'],
            ['label' => 'Subscriure-se', 'url' => '/subscribe'],
          ],
        ],
        [
          'heading' => 'Legal',
          'links' => [
            ['label' => 'Política de Privacitat', 'url' => '/privacy-policy'],
            ['label' => "Condicions d'Ús", 'url' => '/terms'],
          ],
        ],
      ],
      default => [
        [
          'heading' => 'Learn',
          'links' => [
            ['label' => 'Tutorials', 'url' => '/tutorials'],
            ['label' => 'Use Cases', 'url' => '/use-cases'],
            ['label' => 'Articles', 'url' => '/articles'],
            ['label' => 'Tags', 'url' => '/tags'],
          ],
        ],
        [
          'heading' => 'Company',
          'links' => [
            ['label' => 'About', 'url' => '/about'],
            ['label' => 'Subscribe', 'url' => '/subscribe'],
          ],
        ],
        [
          'heading' => 'Legal',
          'links' => [
            ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
            ['label' => 'Terms of Use', 'url' => '/terms'],
          ],
        ],
      ],
    };

    $brand_tagline = match($lang) {
      'es' => $this->t('Automatización con IA para equipos que quieren trabajar menos y lograr más.'),
      'ca' => $this->t("Automatització amb IA per a equips que volen treballar menys i aconseguir més."),
      default => $this->t('AI automation for teams that want to work less and achieve more.'),
    };

    $copyright = match($lang) {
      'es' => $this->t('© @year Sityos. Todos los derechos reservados.', ['@year' => date('Y')]),
      'ca' => $this->t('© @year Sityos. Tots els drets reservats.', ['@year' => date('Y')]),
      default => $this->t('© @year Sityos. All rights reserved.', ['@year' => date('Y')]),
    };

    // Read logo paths from theme settings — same pattern as
    // sityos_automate_olivero_preprocess_block__system_branding_block().
    $theme = 'sityos_automate_olivero';
    $dark_setting  = theme_get_setting('logo_dark_path',  $theme);
    $light_setting = theme_get_setting('logo_light_path', $theme);
    $logo_dark_url  = ($dark_setting  !== NULL && $dark_setting  !== '')
      ? $dark_setting
      : '/sites/default/files/logos/sityos-ai-dark-v2.svg';
    $logo_light_url = ($light_setting !== NULL && $light_setting !== '')
      ? $light_setting
      : '/sites/default/files/logos/sityos-ai-light-v2.svg';

    return [
      '#type' => 'component',
      '#component' => 'sityos_automate_olivero:footer',
      '#props' => [
        'brand_tagline'  => $brand_tagline,
        'nav_columns'    => $nav_columns,
        'copyright'      => $copyright,
        'social_links'   => [],
        'logo_dark_url'  => $logo_dark_url,
        'logo_light_url' => $logo_light_url,
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
