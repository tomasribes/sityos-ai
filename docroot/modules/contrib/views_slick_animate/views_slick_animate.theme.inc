<?php

/**
 * @file
 * Preprocessors and helper functions to make theming easier.
 */
use Drupal\Core\Template\Attribute;
use Drupal\Component\Utility\Html;
use Drupal\views\ViewExecutable;
/**
 * Prepares variables for views grid templates.
 *
 * Default template:slickanimate.html.twig.
 *
 * @param array $vars
 *   An associative array containing:
 *   - view: A ViewExecutable object.
 *   - rows: The raw row data.
 */
function template_preprocess_views_view_slickanimate(array &$vars) {
  $view = $vars['view'];
  $vars['#attached']['library'][] = 'views_slick_animate/slickanimatesettings';
  $vars['id'] = getUniqueId($view);

  $options = $view->style_plugin->options;
  $modelView = $options['views_slick_settings']['animationModel']['modelView'];
  $checkForAnimate = $options['views_slick_settings']['animationModel']['checkForAnimate'];
  $rendomanimate = $view->style_plugin->options['views_slick_settings']['animationModel']['rendomAnimate'];
  $animateConut = 0;
  $animateautoimg = 0;

  if ($modelView == 'card') {
    $wrapper_attributes = ['class' => ['card-group']];
    $vars['attributes'] = new Attribute($wrapper_attributes);
  
    // Card rows.
    $slickSlideImage = $options['views_slick_settings']['animationModel']['slickSlideImage'];
    $slickSlideTitle = $options['views_slick_settings']['animationModel']['slickSlideTitle'];
    $slickSlideSubtitle = $options['views_slick_settings']['animationModel']['slickSlideSubtitle'];
    $slickSlideDescription = $options['views_slick_settings']['animationModel']['slickSlideDescription'];
    $slickSlideButton = $options['views_slick_settings']['animationModel']['slickSlideButton'];
    if ($checkForAnimate) {
      $imageAnimation = $options['views_slick_settings']['animationModel']['imageAnimationType'];
      $titleAnimation = $options['views_slick_settings']['animationModel']['titleAnimationType'];
      $subTitleAnimation = $options['views_slick_settings']['animationModel']['subTitleAnimationType'];
      $descriptionAnimation = $options['views_slick_settings']['animationModel']['descriptionAnimationType'];

      // multiple animate make auto
      $multipleanimate = $view->style_plugin->options['views_slick_settings']['animationModel']['multipleAnimates'];
      $getAnimates = array_keys($multipleanimate);
    }

    foreach ($vars['rows'] as $id => $row) {
      $vars['rows'][$id] = [];
      $vars['rows'][$id]['slickSlideImage'] = $view->style_plugin->getField($id, $slickSlideImage);
      $vars['rows'][$id]['slickSlideTitle'] = $view->style_plugin->getField($id, $slickSlideTitle);
      $vars['rows'][$id]['slickSlideSubtitle'] = $view->style_plugin->getField($id, $slickSlideSubtitle);
      $vars['rows'][$id]['slickSlideDescription'] = $view->style_plugin->getField($id, $slickSlideDescription);
      $vars['rows'][$id]['slickSlideButton'] = $view->style_plugin->getField($id, $slickSlideButton);
      $row_attributes = ['class' => ['card']];
      $vars['rows'][$id]['attributes'] = new Attribute($row_attributes);
      if ($checkForAnimate) {
        if (count($getAnimates) == $animateConut) { $animateConut = 0; }
        $vars['rows'][$id]['imageanimation'] = $imageAnimation;
        $vars['rows'][$id]['titleanimation'] = ($rendomanimate == 0) ? $titleAnimation : $getAnimates[$animateConut];
        $vars['rows'][$id]['subtitleanimation'] = ($rendomanimate == 0) ? $subTitleAnimation : $getAnimates[$animateConut];
        $vars['rows'][$id]['descriptionanimation'] = ($rendomanimate == 0) ? $descriptionAnimation : $getAnimates[$animateConut];
        $animateConut++;
      }
    }
  }elseif($modelView == 'hero_banner'){

    $wrapper_attributes = ['class' => ['hero-banner-slide-show']];
    $vars['attributes'] = new Attribute($wrapper_attributes);
    // banner rows.
    $slickSlideImage = $options['views_slick_settings']['animationModel']['slickSlideImage'];
    $slickSlideTitle = $options['views_slick_settings']['animationModel']['slickSlideTitle'];
    $slickSlideSubtitle = $options['views_slick_settings']['animationModel']['slickSlideSubtitle'];
    $slickSlideDescription = $options['views_slick_settings']['animationModel']['slickSlideDescription'];
    $slickSlideButton = $options['views_slick_settings']['animationModel']['slickSlideButton'];

    if ($checkForAnimate) {
      $imageAnimation = $options['views_slick_settings']['animationModel']['imageAnimationType'];
      $titleAnimation = $options['views_slick_settings']['animationModel']['titleAnimationType'];
      $subTitleAnimation = $options['views_slick_settings']['animationModel']['subTitleAnimationType'];
      $descriptionAnimation = $options['views_slick_settings']['animationModel']['descriptionAnimationType'];

      // multiple animate make auto
      $multipleanimate = $view->style_plugin->options['views_slick_settings']['animationModel']['multipleAnimates'];
      $getAnimates = array_keys($multipleanimate);
    }

    foreach ($vars['rows'] as $id => $row) {
      $vars['rows'][$id] = [];
      $vars['rows'][$id]['slickslideimage'] = $view->style_plugin->getField($id, $slickSlideImage);
      $vars['rows'][$id]['content'] = [];
      $vars['rows'][$id]['content']['slickslidesubtitle'] = $view->style_plugin->getField($id, $slickSlideSubtitle);
      $vars['rows'][$id]['content']['slickslidetitle'] = $view->style_plugin->getField($id, $slickSlideTitle);
      $vars['rows'][$id]['content']['slickslidedescription'] = $view->style_plugin->getField($id, $slickSlideDescription);
      $vars['rows'][$id]['content']['slidebutton'] = $view->style_plugin->getField($id, $slickSlideButton);
      $row_attributes = ['class' => ['slide_hero_banner content-relative']];
      $vars['rows'][$id]['attributes'] = new Attribute($row_attributes);

      if ($checkForAnimate) { // animation type get in key
        $vars['rows'][$id]['imageanimation'] = $imageAnimation;
        if (count($getAnimates) == $animateConut) { $animateConut = 0; }
        if ($animateautoimg == 2) { $animateautoimg = 0; }
        $vars['rows'][$id]['imageanimation'] = ($rendomanimate == 0) ? $imageAnimation : ["zoomInImage","zoomOutImage"][$animateautoimg];
        $vars['rows'][$id]['animation'] = [];
        $vars['rows'][$id]['animation']['slickslidesubtitle'] = ($rendomanimate == 0) ? $subTitleAnimation : $getAnimates[$animateConut];
        $vars['rows'][$id]['animation']['slickslidetitle'] = ($rendomanimate == 0) ? $titleAnimation : $getAnimates[$animateConut];
        $vars['rows'][$id]['animation']['slickslidedescription'] = ($rendomanimate == 0) ? $descriptionAnimation : $getAnimates[$animateConut];;
        $vars['rows'][$id]['animation']['slidebutton'] = ($rendomanimate == 0) ? $titleAnimation : $getAnimates[$animateConut];

        $vars['rows'][$id]['position_content'] = ($rendomanimate == 0) ? $titleAnimation : $getAnimates[$animateConut];

        // auto count plus
        $animateConut++;
        $animateautoimg++;
      }
    }

  }else{
    // default global passing in content 
  }
  
  $vars['slick_styles'] = $options["views_slick_settings"]['styles'];
  $vars['modelview'] = $options['views_slick_settings']['animationModel']['modelView'];
  $vars['animate_make'] = $options['views_slick_settings']['animationModel']['checkForAnimate'];
  
  $options['views_slick_settings']['id'] = $vars['id'];
  $vars['#attached']['drupalSettings']['views_slick_animate']['slick_setting'][$vars['id']] = $options['views_slick_settings'];
}

function getUniqueId(ViewExecutable $view) {
  $id = $view->storage->id() . '-' . $view->current_display;
  return Html::getUniqueId('slickanimate-' . $id);
}
