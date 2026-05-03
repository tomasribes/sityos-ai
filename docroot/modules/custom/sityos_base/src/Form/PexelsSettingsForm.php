<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\sityos_base\Service\PexelsApiService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Admin settings form for the Pexels API integration.
 */
final class PexelsSettingsForm extends ConfigFormBase {

  protected PexelsApiService $pexelsApi;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    PexelsApiService $pexelsApi,
  ) {
    parent::__construct($config_factory, $typed_config_manager);
    $this->pexelsApi = $pexelsApi;
  }

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('sityos_base.pexels_api'),
    );
  }

  public function getFormId(): string {
    return 'sityos_base_pexels_settings';
  }

  protected function getEditableConfigNames(): array {
    return ['sityos_base.settings.pexels'];
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $has_key = $this->pexelsApi->hasApiKey();

    $form['api_section'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('API Configuration'),
    ];

    $form['api_section']['api_key'] = [
      '#type' => 'password',
      '#title' => $this->t('Pexels API Key'),
      '#description' => $this->t(
        'Free API key available at <a href="https://www.pexels.com/api/" target="_blank" rel="noreferrer noopener">pexels.com/api</a>. Leave blank to keep the existing key.'
      ),
      '#placeholder' => $has_key
        ? $this->t('(key saved — leave blank to keep it)')
        : $this->t('Paste your API key here'),
      '#maxlength' => 128,
      '#attributes' => ['autocomplete' => 'off'],
    ];

    $form['api_section']['connection_status'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'pexels-connection-status'],
    ];

    $form['api_section']['test_connection'] = [
      '#type' => 'button',
      '#value' => $this->t('Test Connection'),
      '#ajax' => [
        'callback' => '::testConnectionAjax',
        'wrapper' => 'pexels-connection-status',
        'effect' => 'fade',
        'progress' => ['type' => 'throbber', 'message' => $this->t('Testing…')],
      ],
      '#limit_validation_errors' => [['api_key']],
    ];

    if ($has_key) {
      $import_url = Url::fromRoute('sityos_base.pexels_import')->toString();
      $form['import_link'] = [
        '#markup' => $this->t(
          'API key configured. <a href="@url">Search and import images →</a>',
          ['@url' => $import_url]
        ),
        '#prefix' => '<div class="messages messages--status" style="margin-top: 1.5rem;">',
        '#suffix' => '</div>',
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  public function testConnectionAjax(array &$form, FormStateInterface $form_state): AjaxResponse {
    $api_key = (string) ($form_state->getValue('api_key') ?? '');
    $result = $this->pexelsApi->testConnection($api_key);

    if ($result['ok']) {
      $html = '<div class="messages messages--status">' .
        $this->t('✓ Connected — @remaining API requests remaining this month.', [
          '@remaining' => number_format($result['remaining']),
        ]) .
        '</div>';
    }
    else {
      $html = '<div class="messages messages--error">' .
        $this->t('✗ @message', ['@message' => $result['message']]) .
        '</div>';
    }

    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('#pexels-connection-status', $html));
    return $response;
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $api_key = (string) ($form_state->getValue('api_key') ?? '');
    $config = $this->config('sityos_base.settings.pexels');
    if (!empty($api_key)) {
      $config->set('api_key', $api_key);
    }
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
