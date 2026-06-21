<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @Block(
 *   id = "sityos_footer",
 *   admin_label = @Translation("Sityos: Site Footer"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosFooterBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly MenuLinkTreeInterface $menuLinkTree,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LanguageManagerInterface $languageManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('menu.link_tree'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
    );
  }

  public function build(): array {
    $lang = $this->languageManager->getCurrentLanguage()->getId();

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
        'nav_columns'    => $this->buildNavColumns(),
        'copyright'      => $copyright,
        'social_links'   => [
          [
            'label' => 'LinkedIn',
            'url'   => 'https://www.linkedin.com/company/sityos',
            'icon'  => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
          ],
          [
            'label' => 'X / Twitter',
            'url'   => 'https://x.com/sityos',
            'icon'  => '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.835L1.254 2.25H8.08l4.26 5.632L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
          ],
        ],
        'logo_dark_url'  => $logo_dark_url,
        'logo_light_url' => $logo_light_url,
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface', 'user.permissions'],
        'tags' => [
          'config:system.menu.footer', 'menu:footer',
          'config:system.menu.company', 'menu:company',
          'config:system.menu.legal', 'menu:legal',
          'config:system.site',
        ],
        'max-age' => Cache::PERMANENT,
      ],
    ];
  }

  private function buildNavColumns(): array {
    $menu_storage = $this->entityTypeManager->getStorage('menu');
    $parameters = new MenuTreeParameters();
    $parameters->setMaxDepth(1);
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $columns = [];

    foreach (['footer', 'company', 'legal'] as $menu_id) {
      $menu_entity = $menu_storage->load($menu_id);
      if ($menu_entity === NULL) {
        continue;
      }

      $tree = $this->menuLinkTree->load($menu_id, $parameters);
      $tree = $this->menuLinkTree->transform($tree, $manipulators);

      $links = [];
      foreach ($tree as $element) {
        if (!$element->link->isEnabled() || !$element->access->isAllowed()) {
          continue;
        }
        $links[] = [
          'label' => $element->link->getTitle(),
          'url'   => $element->link->getUrlObject()->toString(),
        ];
      }

      $columns[] = [
        'heading' => $menu_entity->label(),
        'links'   => $links,
      ];
    }

    return $columns;
  }

  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages:language_interface', 'user.permissions']);
  }

}
