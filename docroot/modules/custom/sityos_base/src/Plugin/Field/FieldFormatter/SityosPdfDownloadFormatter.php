<?php

declare(strict_types=1);

namespace Drupal\sityos_base\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @FieldFormatter(
 *   id = "sityos_pdf_download",
 *   label = @Translation("Sityos: PDF Download Button"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class SityosPdfDownloadFormatter extends FormatterBase {

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    protected readonly FileUrlGeneratorInterface $fileUrlGenerator,
    protected readonly EntityRepositoryInterface $entityRepository,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('file_url_generator'),
      $container->get('entity.repository'),
    );
  }

  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    foreach ($items as $delta => $item) {
      /** @var \Drupal\media\MediaInterface|null $media */
      $media = $item->entity;
      if (!$media) {
        continue;
      }
      // Resolve the correct language translation — same pattern as core
      // EntityReferenceFormatterBase::getEntitiesToView().
      $media = $this->entityRepository->getTranslationFromContext($media, $langcode);
      if (!$media->access('view')) {
        continue;
      }

      $source_config = $media->getSource()->getConfiguration();
      $source_field = $source_config['source_field'] ?? NULL;
      if (!$source_field || !$media->hasField($source_field)) {
        continue;
      }

      /** @var \Drupal\file\FileInterface|null $file */
      $file = $media->get($source_field)->entity;
      if (!$file) {
        continue;
      }

      $elements[$delta] = [
        '#type' => 'inline_template',
        '#template' => '
          <a href="{{ url }}" class="sityos-pdf-download" download="{{ filename }}" rel="noopener">
            <span class="sityos-pdf-download__icon" aria-hidden="true">{{ icon|raw }}</span>
            <span class="sityos-pdf-download__text">
              <span class="sityos-pdf-download__label">{{ label }}</span>
              <span class="sityos-pdf-download__filename">{{ filename }}</span>
            </span>
          </a>',
        '#context' => [
          'url' => $this->fileUrlGenerator->generateAbsoluteString($file->getFileUri()),
          'filename' => $file->getFilename(),
          'label' => $this->t('Download PDF'),
          'icon' => $this->getPdfSvg(),
        ],
      ];
    }
    return $elements;
  }

  private function getPdfSvg(): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>';
  }

}
