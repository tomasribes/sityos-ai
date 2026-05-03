<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Admin settings form for the TagCloud block rendering mode.
 */
final class TagCloudSettingsForm extends ConfigFormBase {

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
  ) {
    parent::__construct($config_factory, $typed_config_manager);
  }

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
    );
  }

  public function getFormId(): string {
    return 'sityos_base_tagcloud_settings';
  }

  protected function getEditableConfigNames(): array {
    return ['sityos_base.settings.tagcloud'];
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['rendering_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('TagCloud rendering mode'),
      '#options' => [
        'css' => $this->t('DOM con colores y tamaños (Phase 1) — flex-wrap list with color palette and fluid font sizes.'),
        'canvas' => $this->t('Renderizado JS + canvas (Phase 2) — wordcloud2.js canvas overlay; DOM list kept for SEO.'),
      ],
      '#default_value' => $this->config('sityos_base.settings.tagcloud')->get('rendering_mode'),
    ];
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('sityos_base.settings.tagcloud')
      ->set('rendering_mode', $form_state->getValue('rendering_mode'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
