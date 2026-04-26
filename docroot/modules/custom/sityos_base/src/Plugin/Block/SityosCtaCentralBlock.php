<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Url;

/**
 * @Block(
 *   id = "sityos_cta_central",
 *   admin_label = @Translation("Sityos: CTA Central"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosCtaCentralBlock extends BlockBase {

  public function build(): array {
    $language = \Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_URL);
    $subscribe_url = Url::fromUserInput('/subscribe', ['language' => $language])->toString();
    $tutorials_url = Url::fromUserInput('/tutorials', ['language' => $language])->toString();

    return [
      '#type' => 'component',
      '#component' => 'sityos_automate_olivero:cta-central',
      '#props' => [
        'headline' => $this->t('Ready to Automate Your Workflow?'),
        'body' => $this->t('Join professionals using Sityos Automate to work smarter. Get weekly tutorials, use cases, and automation insights — free.'),
        'cta_primary_label' => $this->t('Subscribe Free →'),
        'cta_primary_url' => $subscribe_url,
        'cta_secondary_label' => $this->t('Explore Content First'),
        'cta_secondary_url' => $tutorials_url,
        'microcopy' => $this->t('No spam. Unsubscribe anytime.'),
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'max-age' => Cache::PERMANENT,
      ],
    ];
  }

  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['languages:language_interface']);
  }

}
