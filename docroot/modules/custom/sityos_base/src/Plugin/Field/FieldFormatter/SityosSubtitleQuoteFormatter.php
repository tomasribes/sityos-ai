<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * @FieldFormatter(
 *   id = "sityos_subtitle_quote",
 *   label = @Translation("Sityos: Subtitle Quote (h2)"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class SityosSubtitleQuoteFormatter extends FormatterBase {

  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    foreach ($items as $delta => $item) {
      if (empty($item->value)) {
        continue;
      }
      $elements[$delta] = [
        '#type' => 'inline_template',
        '#template' => '<div class="sityos-subtitle-quote"><h2 class="sityos-subtitle-quote__text">{{ value }}</h2></div>',
        '#context' => ['value' => $item->value],
      ];
    }
    return $elements;
  }

}
