<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\sityos_base\Service\PexelsApiService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Editor-facing form to search Pexels and import images to the Media Library.
 */
final class PexelsImportForm extends FormBase {

  protected PexelsApiService $pexelsApi;

  public function __construct(PexelsApiService $pexelsApi) {
    $this->pexelsApi = $pexelsApi;
  }

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('sityos_base.pexels_api'),
    );
  }

  public function getFormId(): string {
    return 'sityos_base_pexels_import';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#attached']['library'][] = 'sityos_base/pexels-import';

    if (!$this->pexelsApi->hasApiKey()) {
      $settings_url = Url::fromRoute('sityos_base.pexels_settings')->toString();
      $form['no_key'] = [
        '#markup' => $this->t(
          'No Pexels API key configured. <a href="@url">Configure it here</a>.',
          ['@url' => $settings_url]
        ),
        '#prefix' => '<div class="messages messages--warning">',
        '#suffix' => '</div>',
      ];
      return $form;
    }

    // Search section.
    $form['search'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Search Pexels'),
      '#attributes' => ['class' => ['pexels-search-bar']],
    ];

    $form['search']['query'] = [
      '#type' => 'search',
      '#title' => $this->t('Keywords'),
      '#title_display' => 'invisible',
      '#placeholder' => $this->t('e.g. artificial intelligence, automation, technology office'),
      '#size' => 50,
      '#default_value' => $form_state->get('current_query') ?? '',
      '#attributes' => ['autocomplete' => 'off'],
    ];

    $form['search']['per_page'] = [
      '#type' => 'select',
      '#title' => $this->t('Per page'),
      '#options' => [12 => 12, 20 => 20, 30 => 30],
      '#default_value' => $form_state->get('current_per_page') ?? 20,
    ];

    $form['search']['search_btn'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      '#submit' => ['::executeSearch'],
      '#ajax' => [
        'callback' => '::resultsAjax',
        'wrapper' => 'pexels-results-wrapper',
        'effect' => 'fade',
        'progress' => ['type' => 'throbber', 'message' => $this->t('Searching…')],
      ],
      '#limit_validation_errors' => [['query'], ['per_page']],
      '#button_type' => 'primary',
    ];

    // Results container — rebuilt on every AJAX response.
    $photos = $form_state->get('photos') ?? [];
    $total_results = (int) ($form_state->get('total_results') ?? 0);
    $current_page = (int) ($form_state->get('current_page') ?? 1);
    $current_per_page = (int) ($form_state->get('current_per_page') ?? 20);
    $import_result = $form_state->get('import_result');

    $form['results_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'pexels-results-wrapper'],
    ];

    if (!empty($photos)) {
      $this->buildResultsSection($form['results_wrapper'], $photos, $total_results, $current_page, $current_per_page, $import_result);
    }

    return $form;
  }

  private function buildResultsSection(
    array &$wrapper,
    array $photos,
    int $total_results,
    int $current_page,
    int $per_page,
    ?array $import_result,
  ): void {
    $total_pages = max(1, (int) ceil($total_results / $per_page));

    // Import result message.
    if ($import_result !== NULL) {
      $wrapper['import_message'] = [
        '#markup' => $this->buildImportMessage($import_result),
      ];
    }

    // Summary.
    $wrapper['summary'] = [
      '#markup' => '<p class="pexels-summary">' .
        $this->t('@total images found — page @page of @pages', [
          '@total' => number_format($total_results),
          '@page' => $current_page,
          '@pages' => $total_pages,
        ]) .
        '</p>',
    ];

    // Photo grid with individual checkboxes.
    $wrapper['selection'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['pexels-grid']],
      '#tree' => TRUE,
    ];

    foreach ($photos as $photo) {
      $photo_id = (int) $photo['id'];
      $is_imported = $this->pexelsApi->isAlreadyImported($photo_id);
      $alt = Html::escape((string) ($photo['alt'] ?: $photo['photographer'] ?: ''));
      $photographer = Html::escape((string) ($photo['photographer'] ?? ''));
      $thumb = Html::escape((string) ($photo['src']['medium'] ?? $photo['src']['small'] ?? ''));
      $pexels_url = Html::escape((string) ($photo['url'] ?? ''));

      $card_classes = 'pexels-card' . ($is_imported ? ' pexels-card--imported' : '');

      $wrapper['selection'][$photo_id] = [
        '#type' => 'checkbox',
        '#title' => $photographer ?: $this->t('Unknown'),
        '#disabled' => $is_imported,
        '#default_value' => FALSE,
        '#prefix' => '<div class="' . $card_classes . '">' .
          '<div class="pexels-card__thumb">' .
          '<img src="' . $thumb . '" alt="' . $alt . '" loading="lazy">' .
          ($is_imported ? '<span class="pexels-card__badge">' . $this->t('Imported') . '</span>' : '') .
          '</div>' .
          '<div class="pexels-card__meta"><span class="pexels-card__alt">' . $alt . '</span>',
        '#suffix' => ($pexels_url
          ? '<a href="' . $pexels_url . '" target="_blank" rel="noreferrer noopener" class="pexels-card__link">' . $this->t('Pexels') . ' ↗</a>'
          : '') .
          '</div></div>',
      ];
    }

    // Pagination row.
    $wrapper['pagination'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['pexels-pagination']],
    ];

    if ($current_page > 1) {
      $wrapper['pagination']['prev'] = [
        '#type' => 'submit',
        '#value' => $this->t('← Previous'),
        '#name' => 'pexels_prev',
        '#submit' => ['::goToPreviousPage'],
        '#ajax' => [
          'callback' => '::resultsAjax',
          'wrapper' => 'pexels-results-wrapper',
          'effect' => 'fade',
          'progress' => ['type' => 'throbber', 'message' => $this->t('Loading…')],
        ],
        '#limit_validation_errors' => [],
      ];
    }

    $wrapper['pagination']['page_info'] = [
      '#markup' => '<span class="pexels-pagination__info">' .
        $this->t('Page @page of @total', ['@page' => $current_page, '@total' => $total_pages]) .
        '</span>',
    ];

    if ($current_page < $total_pages) {
      $wrapper['pagination']['next'] = [
        '#type' => 'submit',
        '#value' => $this->t('Next →'),
        '#name' => 'pexels_next',
        '#submit' => ['::goToNextPage'],
        '#ajax' => [
          'callback' => '::resultsAjax',
          'wrapper' => 'pexels-results-wrapper',
          'effect' => 'fade',
          'progress' => ['type' => 'throbber', 'message' => $this->t('Loading…')],
        ],
        '#limit_validation_errors' => [],
      ];
    }

    // Import actions.
    $wrapper['import_actions'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['pexels-import-actions']],
    ];

    $wrapper['import_actions']['import_btn'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import Selected'),
      '#name' => 'pexels_import',
      '#submit' => ['::importSelected'],
      '#ajax' => [
        'callback' => '::resultsAjax',
        'wrapper' => 'pexels-results-wrapper',
        'effect' => 'fade',
        'progress' => ['type' => 'throbber', 'message' => $this->t('Importing…')],
      ],
      '#button_type' => 'primary',
    ];
  }

  private function buildImportMessage(array $result): string {
    if (isset($result['warning'])) {
      return '<div class="messages messages--warning">' . Html::escape((string) $result['warning']) . '</div>';
    }

    $parts = [];
    if (!empty($result['imported'])) {
      $parts[] = (string) $this->formatPlural((int) $result['imported'], '1 image imported', '@count images imported');
    }
    if (!empty($result['skipped'])) {
      $parts[] = (string) $this->formatPlural((int) $result['skipped'], '1 already existed', '@count already existed');
    }
    if (!empty($result['errors'])) {
      $parts[] = (string) $this->formatPlural((int) $result['errors'], '1 error', '@count errors');
    }

    $type = !empty($result['errors']) ? 'warning' : 'status';
    $media_url = Url::fromRoute('entity.media.collection')->toString();
    $summary = implode(' · ', $parts);

    return '<div class="messages messages--' . $type . '">' .
      $summary .
      ' — <a href="' . $media_url . '">' . $this->t('View Media Library') . ' →</a>' .
      '</div>';
  }

  // Submit handlers.

  public function executeSearch(array &$form, FormStateInterface $form_state): void {
    $query = trim((string) ($form_state->getValue('query') ?? ''));
    $per_page = (int) ($form_state->getValue('per_page') ?? 20);

    if (empty($query)) {
      $form_state->setError($form['search']['query'], $this->t('Please enter a search term.'));
      return;
    }

    $results = $this->pexelsApi->search($query, $per_page, 1);
    $form_state->set('photos', $results['photos']);
    $form_state->set('total_results', $results['total_results']);
    $form_state->set('current_query', $query);
    $form_state->set('current_per_page', $per_page);
    $form_state->set('current_page', 1);
    $form_state->set('import_result', NULL);
    $form_state->setRebuild(TRUE);
  }

  public function goToNextPage(array &$form, FormStateInterface $form_state): void {
    $this->fetchPage($form_state, ((int) $form_state->get('current_page')) + 1);
  }

  public function goToPreviousPage(array &$form, FormStateInterface $form_state): void {
    $this->fetchPage($form_state, max(1, ((int) $form_state->get('current_page')) - 1));
  }

  private function fetchPage(FormStateInterface $form_state, int $page): void {
    $results = $this->pexelsApi->search(
      (string) $form_state->get('current_query'),
      (int) $form_state->get('current_per_page'),
      $page,
    );
    $form_state->set('photos', $results['photos']);
    $form_state->set('total_results', $results['total_results']);
    $form_state->set('current_page', $page);
    $form_state->set('import_result', NULL);
    $form_state->setRebuild(TRUE);
  }

  public function importSelected(array &$form, FormStateInterface $form_state): void {
    $selection = $form_state->getValue('selection') ?? [];
    $selected_ids = array_keys(array_filter((array) $selection));

    if (empty($selected_ids)) {
      $form_state->set('import_result', ['warning' => $this->t('No images selected.')]);
      $form_state->setRebuild(TRUE);
      return;
    }

    $photos = $form_state->get('photos') ?? [];
    $photos_by_id = [];
    foreach ($photos as $photo) {
      $photos_by_id[(int) $photo['id']] = $photo;
    }

    $imported = 0;
    $skipped = 0;
    $errors = 0;

    foreach ($selected_ids as $photo_id) {
      $photo_id = (int) $photo_id;
      if (!isset($photos_by_id[$photo_id])) {
        $errors++;
        continue;
      }

      $result = $this->pexelsApi->importPhoto($photos_by_id[$photo_id]);
      match ($result['status']) {
        'imported' => $imported++,
        'skipped' => $skipped++,
        default => $errors++,
      };
    }

    // Clear selection from user input so checkboxes reset.
    $user_input = $form_state->getUserInput();
    unset($user_input['selection']);
    $form_state->setUserInput($user_input);

    $form_state->set('import_result', [
      'imported' => $imported,
      'skipped' => $skipped,
      'errors' => $errors,
    ]);
    $form_state->setRebuild(TRUE);
  }

  // AJAX callbacks.

  public function resultsAjax(array &$form, FormStateInterface $form_state): array {
    return $form['results_wrapper'];
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // All actions use custom #submit handlers; this is intentionally empty.
  }

}
