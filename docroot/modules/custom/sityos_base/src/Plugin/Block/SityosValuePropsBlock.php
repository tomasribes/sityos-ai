<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Url;

/**
 * @Block(
 *   id = "sityos_value_props",
 *   admin_label = @Translation("Sityos: Value Propositions"),
 *   category = @Translation("Sityos")
 * )
 */
final class SityosValuePropsBlock extends BlockBase {

  public function build(): array {
    $language = \Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_URL);
    $tutorials_url = Url::fromUserInput('/tutorials', ['language' => $language])->toString();
    $use_cases_url = Url::fromUserInput('/use-cases', ['language' => $language])->toString();

    return [
      '#type' => 'component',
      '#component' => 'sityos_automate_olivero:feature-grid',
      '#props' => [
        'heading' => $this->t('Why teams choose Sityos Automate'),
        'columns' => 3,
        'variant' => 'cards',
        'items' => [
          [
            'icon' => '⚡',
            'title' => $this->t('Practical AI Workflows'),
            'description' => $this->t('Step-by-step guides built for real work environments. No theory — just actionable automation playbooks you can implement today.'),
            'url' => $tutorials_url,
          ],
          [
            'icon' => '🎯',
            'title' => $this->t('Real-World Use Cases'),
            'description' => $this->t('Learn from documented implementations across industries. See exactly how teams are using AI to cut costs and accelerate delivery.'),
            'url' => $use_cases_url,
          ],
          [
            'icon' => '📚',
            'title' => $this->t('Expert-Curated Tutorials'),
            'description' => $this->t('From beginner to advanced. Our tutorials cover the full automation stack — from prompt engineering to multi-agent orchestration.'),
            'url' => $tutorials_url,
          ],
        ],
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
