<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;

/**
 * @FieldFormatter(
 *   id = "sityos_tags_box",
 *   label = @Translation("Sityos: Tags as Boxes"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class SityosTagsBoxFormatter extends FormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $tags = [];
    foreach ($items as $item) {
      /** @var \Drupal\taxonomy\TermInterface|null $term */
      $term = $item->entity;
      if (!$term || !$term->access('view')) {
        continue;
      }
      $tags[] = [
        'url' => Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $term->id()])->toString(),
        'name' => $term->label(),
      ];
    }

    if (empty($tags)) {
      return [];
    }

    return [
      [
        '#type' => 'inline_template',
        '#template' => '<div class="sityos-tag-list">{% for tag in tags %}<a href="{{ tag.url }}" class="sityos-tag-list__item">{{ tag.name }}</a>{% endfor %}</div>',
        '#context' => ['tags' => $tags],
      ],
    ];
  }

}
