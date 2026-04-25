<?php

/**
 * @file
 * Custom theme settings for Sityos Automate Olivero.
 *
 * Adds logo_dark and logo_light upload + path fields to the theme settings
 * form at admin/appearance/settings/sityos_automate_olivero.
 */

declare(strict_types=1);

use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function sityos_automate_olivero_form_system_theme_settings_alter(array &$form, FormStateInterface $form_state): void {
  $form['sao_logo_variants'] = [
    '#type' => 'details',
    '#title' => t('Logo variants (dark / light mode)'),
    '#open' => TRUE,
    '#description' => t('Upload separate logos for dark and light color modes. Leave blank to use the theme defaults from the <code>images/</code> directory.'),
    '#weight' => -5,
  ];

  $variants = [
    'dark'  => t('Dark mode logo'),
    'light' => t('Light mode logo'),
  ];

  foreach ($variants as $variant => $label) {
    $path_key   = "logo_{$variant}_path";
    $upload_key = "logo_{$variant}_upload";

    $form['sao_logo_variants'][$path_key] = [
      '#type'          => 'textfield',
      '#title'         => $label . ' — ' . t('current path'),
      '#default_value' => theme_get_setting($path_key) ?? '',
      '#description'   => t('Relative URL. Populated automatically on upload; edit manually to override.'),
    ];

    $form['sao_logo_variants'][$upload_key] = [
      '#type'        => 'file',
      '#title'       => t('Upload @variant mode logo', ['@variant' => $variant]),
      '#description' => t('Allowed: SVG, PNG, WebP, JPEG. Replaces the path above on save.'),
    ];
  }

  $form['#validate'][] = '_sao_logo_settings_validate';
  $form['#submit'][]   = '_sao_logo_settings_submit';
}

/**
 * Validate handler — checks uploaded file extension.
 *
 * Extension-based check is used instead of MIME type because PHP's finfo
 * inconsistently reports SVGs as text/xml, application/xml, or text/html
 * depending on file content and server config.
 */
function _sao_logo_settings_validate(array &$form, FormStateInterface $form_state): void {
  $allowed_ext = ['svg', 'png', 'webp', 'jpg', 'jpeg'];
  $uploads     = \Drupal::request()->files->get('files', []);

  foreach (['logo_dark_upload', 'logo_light_upload'] as $field) {
    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $file */
    $file = $uploads[$field] ?? NULL;
    if (!$file || !$file->isValid()) {
      continue;
    }
    $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext, TRUE)) {
      $form_state->setErrorByName($field, t('Only SVG, PNG, WebP, or JPEG files are allowed.'));
    }
  }
}

/**
 * Submit handler — saves uploaded files to public://logos/ and stores the URL.
 */
function _sao_logo_settings_submit(array &$form, FormStateInterface $form_state): void {
  /** @var \Drupal\Core\File\FileSystemInterface $file_system */
  $file_system = \Drupal::service('file_system');
  /** @var \Drupal\Core\File\FileUrlGeneratorInterface $url_generator */
  $url_generator = \Drupal::service('file_url_generator');

  $destination = 'public://logos/';
  $file_system->prepareDirectory(
    $destination,
    FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS
  );

  $uploads = \Drupal::request()->files->get('files', []);

  $pairs = [
    'logo_dark_upload'  => 'logo_dark_path',
    'logo_light_upload' => 'logo_light_path',
  ];

  foreach ($pairs as $upload_field => $path_field) {
    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $file */
    $file = $uploads[$upload_field] ?? NULL;
    if (!$file || !$file->isValid()) {
      continue;
    }

    $uri = $file_system->saveData(
      (string) file_get_contents($file->getPathname()),
      $destination . $file->getClientOriginalName(),
      FileExists::Replace
    );

    if ($uri !== FALSE) {
      $form_state->setValue($path_field, $url_generator->generateString($uri));
    }
  }
}
