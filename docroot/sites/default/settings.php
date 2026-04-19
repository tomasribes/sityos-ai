<?php

/**
 * @file
 * Drupal 11 settings for Sityos Automate.
 *
 * Do not put environment-specific configuration here.
 * Use settings.local.php for local overrides.
 */

declare(strict_types=1);

// Config sync directory.
$settings['config_sync_directory'] = '../config/sync';

// Hash salt — loaded from environment, never hardcoded.
$settings['hash_salt'] = getenv('DRUPAL_HASH_SALT') ?: 'MISSING_HASH_SALT';

// Trusted host patterns.
$settings['trusted_host_patterns'] = [
  '^localhost$',
  '^sityos\.local$',
  '^sityos\.com$',
  '^.*\.sityos\.com$',
];

// File system paths.
$settings['file_public_path'] = 'sites/default/files';
$settings['file_private_path'] = '../../private-files';
$settings['file_temp_path'] = '/tmp';

// Prevent accidental cron during local development.
$config['automated_cron.settings']['interval'] = 0;

// Environment detection and config split activation.
$environment = getenv('APP_ENV') ?: 'dev';

switch ($environment) {
  case 'live':
    $config['config_split.config_split.live']['status'] = TRUE;
    break;
  case 'test':
    $config['config_split.config_split.test']['status'] = TRUE;
    break;
  case 'dev':
  default:
    $config['config_split.config_split.dev']['status'] = TRUE;
    break;
}

// Load local settings if present (never committed to git).
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
