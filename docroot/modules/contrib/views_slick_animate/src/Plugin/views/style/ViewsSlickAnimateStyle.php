<?php

namespace Drupal\views_slick_animate\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render a list of years and months
 * in reverse chronological order linked to content.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "slickanimate",
 *   title = @Translation("Views slick Animation"),
 *   help = @Translation("Render a listof fields to content."),
 *   theme = "slickanimate",
 *   theme_file = "../views_slick_animate.theme.inc",
 *   display_types = { "normal" }
 * )
 *
 */
class ViewsSlickAnimateStyle extends StylePluginBase {
  /**
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::usesRowPlugin.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::usesRowClass.
   *
   * @var bool
   */
  protected $usesRowClass = TRUE;

  /**
   * Definition.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['views_slick_settings'] = ['default' => null];
    $options['views_slick_settings']['styles']= ['default' => null];
    $options['views_slick_settings']['autoWidth'] = ['default' => null];
    $options['views_slick_settings']['autoplay'] = ['default' => null];
    $options['views_slick_settings']['autoplaySpeed'] = ['default' => null];
    $options['views_slick_settings']['arrows'] = ['default' => null];
    $options['views_slick_settings']['centerMode'] = ['default' => null];
    $options['views_slick_settings']['centerPadding'] = ['default' => null];
    $options['views_slick_settings']['dots'] = ['default' => null];
    $options['views_slick_settings']['infinite'] = ['default' => null];
    $options['views_slick_settings']['initialSlide'] = ['default' => null];
    $options['views_slick_settings']['lazyLoad'] = ['default' => null];
    $options['views_slick_settings']['mobileFirst'] = ['default' => null];
    $options['views_slick_settings']['slidesToShow'] = ['default' => null];
    $options['views_slick_settings']['slidesToScroll'] = ['default' => null];
    $options['views_slick_settings']['speed'] = ['default' => null];
    $options['views_slick_settings']['variableWidth'] = ['default' => null];
    // Responce settings
    $options['views_slick_settings']['responsive'] = ['default' => null];
    $options['views_slick_settings']['responsive']['mobile'] = ['default' => null];
    $options['views_slick_settings']['responsive']['mobile']['breakpoint'] = ['default' => null];
    $options['views_slick_settings']['responsive']['mobile']['slidesToShow'] = ['default' => null];
    $options['views_slick_settings']['responsive']['mobile']['slidesToScroll'] = ['default' => null];
    $options['views_slick_settings']['responsive']['mobile']['centerMode']  = ['default' => null];
    $options['views_slick_settings']['responsive']['mobile']['centerPadding'] = ['default' => null];
    
    $options['views_slick_settings']['responsive']['tablet'] = ['default' => null];
    $options['views_slick_settings']['responsive']['tablet']['breakpoint'] = ['default' => null];
    $options['views_slick_settings']['responsive']['tablet']['slidesToShow'] = ['default' => null];
    $options['views_slick_settings']['responsive']['tablet']['slidesToScroll'] = ['default' => null];
    $options['views_slick_settings']['responsive']['tablet']['centerMode'] = ['default' => null];
    $options['views_slick_settings']['responsive']['tablet']['centerPadding'] = ['default' => null];
    $options['views_slick_settings']['responsive']['desktop'] = ['default' => null];
    $options['views_slick_settings']['responsive']['desktop']['breakpoint'] = ['default' => null];
    $options['views_slick_settings']['responsive']['desktop']['slidesToShow'] = ['default' => null];
    $options['views_slick_settings']['responsive']['desktop']['slidesToScroll'] = ['default' => null];
    $options['views_slick_settings']['responsive']['desktop']['centerMode'] = ['default' => null];
    $options['views_slick_settings']['responsive']['desktop']['centerPadding'] = ['default' => null];
    // Additional settings.
    $options['views_slick_settings']['additional'] = ['default' => null];
    $options['views_slick_settings']['additional']['accessibility'] = ['default' => null];
    $options['views_slick_settings']['additional']['adaptiveHeight'] = ['default' => null];
    $options['views_slick_settings']['additional']['draggable'] = ['default' => null];
    $options['views_slick_settings']['additional']['cssEase'] = ['default' => null];
    $options['views_slick_settings']['additional']['fade'] = ['default' => null];
    $options['views_slick_settings']['additional']['focusOnSelect'] = ['default' => null];
    $options['views_slick_settings']['additional']['easing'] = ['default' => null];
    $options['views_slick_settings']['additional']['edgeFriction'] = ['default' => null];
    $options['views_slick_settings']['additional']['pauseOnFocus'] = ['default' => null];
    $options['views_slick_settings']['additional']['pauseOnHover'] = ['default' => null];
    $options['views_slick_settings']['additional']['pauseOnDotsHover'] = ['default' => null];
    $options['views_slick_settings']['additional']['respondTo'] = ['default' => null];
    $options['views_slick_settings']['additional']['rows'] = ['default' => null];
    $options['views_slick_settings']['additional']['slidesPerRow'] = ['default' => null];
    $options['views_slick_settings']['additional']['swipe'] = ['default' => null];
    $options['views_slick_settings']['additional']['swipeToSlide'] = ['default' => null];
    $options['views_slick_settings']['additional']['touchMove'] = ['default' => null];
    $options['views_slick_settings']['additional']['touchThreshold'] = ['default' => null];
    $options['views_slick_settings']['additional']['useCSS'] = ['default' => null];
    $options['views_slick_settings']['additional']['useTransform'] = ['default' => null];
    $options['views_slick_settings']['additional']['vertical'] = ['default' => null];
    $options['views_slick_settings']['additional']['verticalSwiping'] = ['default' => null];
    $options['views_slick_settings']['additional']['rtl'] = ['default' => null];
    $options['views_slick_settings']['additional']['waitForAnimate'] = ['default' => null];
    $options['views_slick_settings']['additional']['zIndex'] = ['default' => null];
    $options['views_slick_settings']['animationModel'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['checkForAnimate'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['modelView'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['slickSlideImage'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['imageAnimationType'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['slickSlideTitle'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['titleAnimationType'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['slickSlideSubtitle'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['subTitleAnimationType'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['slickSlideDescription'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['descriptionAnimationType'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['slickSlideButton'] = ['default' => null];

    $options['views_slick_settings']['animationModel']['rendomAnimate'] = ['default' => null];
    $options['views_slick_settings']['animationModel']['multipleAnimates'] = ['default' => []];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    // Responsive settings.
    $form['views_slick_settings']= [
      '#type' => 'details',
      '#title' => $this->t('Views Slick settings'),
      '#open' => TRUE,
    ];

    $form['views_slick_settings']['styles'] = [
      '#title' => $this->t('Slider Style'),
      '#type' => 'radios',
      '#options' => [
        'basic' => $this->t('Basic'),
        'without_styles' => $this->t('Without styles'),
      ],
      '#default_value' =>$this->options['views_slick_settings']['styles'] ?? 'basic',
      '#description' => $this->t('Select additional styles for slick slider'),
    ];

    $form['views_slick_settings']['autoWidth'] = [
      '#title' => $this->t('Auto width'),
      '#type' => 'checkbox',
      '#default_value' =>$this->options['views_slick_settings']['autoWidth'] ?? NULL,
      '#description' => $this->t('If checked, the width of each slide will be its natural width as a inline-block box'),
    ];

    $form['views_slick_settings']['autoplay'] = [
      '#title' => $this->t('Autoplay'),
      '#type' => 'checkbox',
      '#default_value' =>$this->options['views_slick_settings']['autoplay'] ?? NULL,
      '#description' => $this->t('Enables Autoplay'),
      '#attributes' => [
        'class' => ['slick-autoplay-field'],
      ],
    ];

    $form['views_slick_settings']['autoplaySpeed'] = [
      '#title' => $this->t('Autoplay Speed'),
      '#type' => 'number',
      '#default_value' =>$this->options['views_slick_settings']['autoplaySpeed'] ?? '3000',
      '#description' => $this->t('Autoplay Speed in milliseconds'),
       // Set the field as "read-only" when the "autoplay" is unchecked.
      '#states' => [
        'disabled' => [
          ':input.slick-autoplay-field' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['views_slick_settings']['arrows'] = [
      '#title' => $this->t('Arrows'),
      '#type' => 'checkbox',
      '#default_value' =>$this->options['views_slick_settings']['arrows'] ?? 1,
      '#description' => $this->t('Prev/Next Arrows'),
    ];

    $form['views_slick_settings']['centerMode'] = [
      '#title' => $this->t('Center Mode'),
      '#type' => 'checkbox',
      '#default_value' =>$this->options['views_slick_settings']['centerMode'] ?? NULL,
      '#description' => $this->t('Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts'),
    ];

    $form['views_slick_settings']['centerPadding'] = [
      '#title' => $this->t('Center Padding'),
      '#type' => 'textfield',
      '#default_value' =>$this->options['views_slick_settings']['centerPadding'] ?? '50px',
      '#description' => $this->t('Side padding when in center mode (px or %)'),
    ];

    $form['views_slick_settings']['dots'] = [
      '#title' => $this->t('Dots'),
      '#type' => 'checkbox',
      '#default_value' =>$this->options['views_slick_settings']['dots'] ?? NULL,
      '#description' => $this->t('Show dot indicators'),
    ];

    $form['views_slick_settings']['infinite'] = [
      '#title' => $this->t('Infinite'),
      '#type' => 'checkbox',
      '#default_value' =>$this->options['views_slick_settings']['infinite'] ?? 1,
      '#description' => $this->t('Infinite loop sliding'),
    ];

    $form['views_slick_settings']['initialSlide'] = [
      '#title' => $this->t('Initial Slide'),
      '#type' => 'number',
      '#default_value' =>$this->options['views_slick_settings']['initialSlide'] ?? '0',
      '#description' => $this->t('Slide to start on'),
    ];

    $form['views_slick_settings']['lazyLoad'] = [
      '#title' => $this->t('Lazy Load'),
      '#type' => 'radios',
      '#options' => [
        'ondemand' => $this->t('On demand'),
        'progressive' => $this->t('Progressive'),
      ],
      '#default_value' =>$this->options['views_slick_settings']['lazyLoad'] ?? 'ondemand',
      '#description' => $this->t('Set lazy loading technique'),
    ];

    $form['views_slick_settings']['mobileFirst'] = [
      '#title' => $this->t('Mobile First'),
      '#type' => 'checkbox',
      '#default_value' =>$this->options['views_slick_settings']['mobileFirst'] ?? NULL,
      '#description' => $this->t('Responsive settings use mobile first calculation'),
    ];

    $form['views_slick_settings']['slidesToShow'] = [
      '#title' => $this->t('Slides To Show'),
      '#type' => 'number',
      '#default_value' =>$this->options['views_slick_settings']['slidesToShow'] ?? '1',
      '#description' => $this->t('# of slides to show'),
    ];

    $form['views_slick_settings']['slidesToScroll'] = [
      '#title' => $this->t('Slides To Scroll'),
      '#type' => 'number',
      '#default_value' =>$this->options['views_slick_settings']['slidesToScroll'] ?? '1',
      '#description' => $this->t('# of slides to scroll'),
    ];

    $form['views_slick_settings']['speed'] = [
      '#title' => $this->t('Speed'),
      '#type' => 'number',
      '#default_value' =>$this->options['views_slick_settings']['speed'] ?? '300',
      '#description' => $this->t('Slide/Fade animation speed'),
    ];

    $form['views_slick_settings']['variableWidth'] = [
      '#title' => $this->t('Variable Width'),
      '#type' => 'checkbox',
      '#default_value' =>$this->options['views_slick_settings']['variableWidth'] ?? NULL,
      '#description' => $this->t('Variable width slides'),
    ];

    // Responsive settings.
    $form['views_slick_settings']['responsive'] = [
      '#type' => 'details',
      '#title' => $this->t('Responsive settings'),
      '#open' => FALSE,
    ];

    $form['views_slick_settings']['responsive']['mobile'] = [
      '#type' => 'details',
      '#title' => $this->t('Mobile'),
      '#open' => FALSE,
    ];

    $form['views_slick_settings']['responsive']['mobile']['breakpoint'] = [
      '#title' => $this->t('Breakpoint'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['responsive']['mobile']['breakpoint'] ?? '576',
      '#description' => $this->t('The breakpoints behave like (min-width: breakpoint) in CSS, so an undefined option will be inherited from previous small breakpoints.'),
    ];

    $form['views_slick_settings']['responsive']['mobile']['slidesToShow'] = [
      '#title' => $this->t('Slides to show'),
      '#type' => 'number',
      '#default_value' => $this->options['views_slick_settings']['responsive']['mobile']['slidesToShow'] ?? '',
      '#description' => $this->t('Number of slides being displayed in the viewport.'),
    ];

    $form['views_slick_settings']['responsive']['mobile']['slidesToScroll'] = [
      '#title' => $this->t('Slides to scroll'),
      '#type' => 'number',
      '#default_value' => $this->options['views_slick_settings']['responsive']['mobile']['slidesToScroll'] ?? '',
      '#description' => $this->t('Number of slides going on one "click"'),
    ];

    $form['views_slick_settings']['responsive']['mobile']['centerMode'] = [
      '#title' => $this->t('centerPadding'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['responsive']['mobile']['centerPadding'] ?? '',
      '#description' => $this->t('Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts'),
    ];

    $form['views_slick_settings']['responsive']['mobile']['centerPadding'] = [
      '#title' => $this->t('centerPadding'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['responsive']['mobile']['centerPadding'] ?? '',
      '#description' => $this->t('Side padding when in center mode (px or %)'),
    ];

    $form['views_slick_settings']['responsive']['tablet'] = [
      '#type' => 'details',
      '#title' => $this->t('Tablet'),
      '#open' => FALSE,
    ];

    $form['views_slick_settings']['responsive']['tablet']['breakpoint'] = [
      '#title' => $this->t('Breakpoint'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['responsive']['tablet']['breakpoint'] ?? '992',
      '#description' => $this->t('The breakpoints behave like (min-width: breakpoint) in CSS, so an undefined option will be inherited from previous small breakpoints.'),
    ];

    $form['views_slick_settings']['responsive']['tablet']['slidesToShow'] = [
      '#title' => $this->t('Slides to show'),
      '#type' => 'number',
      '#default_value' => $this->options['views_slick_settings']['responsive']['tablet']['slidesToShow'] ?? '',
      '#description' => $this->t('Number of slides being displayed in the viewport.'),
    ];

    $form['views_slick_settings']['responsive']['tablet']['slidesToScroll'] = [
      '#title' => $this->t('Slides to scroll'),
      '#type' => 'number',
      '#default_value' => $this->options['views_slick_settings']['responsive']['tablet']['slidesToScroll'] ?? '',
      '#description' => $this->t('Number of slides going on one "click"'),
    ];

    $form['views_slick_settings']['responsive']['tablet']['centerMode'] = [
      '#title' => $this->t('centerPadding'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['responsive']['tablet']['centerPadding'] ?? '',
      '#description' => $this->t('Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts'),
    ];

    $form['views_slick_settings']['responsive']['tablet']['centerPadding'] = [
      '#title' => $this->t('centerPadding'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['responsive']['tablet']['centerPadding'] ?? '',
      '#description' => $this->t('Side padding when in center mode (px or %)'),
    ];

    $form['views_slick_settings']['responsive']['desktop'] = [
      '#type' => 'details',
      '#title' => $this->t('Desktop'),
      '#open' => FALSE,
    ];

    $form['views_slick_settings']['responsive']['desktop']['breakpoint'] = [
      '#title' => $this->t('Breakpoint'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['responsive']['desktop']['breakpoint'] ?? '1200',
      '#description' => $this->t('The breakpoints behave like (min-width: breakpoint) in CSS, so an undefined option will be inherited from previous small breakpoints.'),
    ];

    $form['views_slick_settings']['responsive']['desktop']['slidesToShow'] = [
      '#title' => $this->t('Slides to show'),
      '#type' => 'number',
      '#default_value' => $this->options['views_slick_settings']['responsive']['desktop']['slidesToShow'] ?? '',
      '#description' => $this->t('Number of slides being displayed in the viewport.'),
    ];

    $form['views_slick_settings']['responsive']['desktop']['slidesToScroll'] = [
      '#title' => $this->t('Slides to scroll'),
      '#type' => 'number',
      '#default_value' => $this->options['views_slick_settings']['responsive']['desktop']['slidesToScroll'] ?? '',
      '#description' => $this->t('Number of slides going on one "click"'),
    ];

    $form['views_slick_settings']['responsive']['desktop']['centerMode'] = [
      '#title' => $this->t('centerPadding'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['responsive']['desktop']['centerPadding'] ?? '',
      '#description' => $this->t('Enables centered view with partial prev/next slides. Use with odd numbered slidesToShow counts'),
    ];

    $form['views_slick_settings']['responsive']['desktop']['centerPadding'] = [
      '#title' => $this->t('centerPadding'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['responsive']['desktop']['centerPadding'] ?? '',
      '#description' => $this->t('Side padding when in center mode (px or %)'),
    ];

    // Additional settings.
    $form['views_slick_settings']['additional'] = [
      '#type' => 'details',
      '#title' => $this->t('Additional settings'),
      '#open' => FALSE,
    ];

    $form['views_slick_settings']['additional']['accessibility'] = [
      '#title' => $this->t('Accessibility'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['accessibility'] ?? 1,
      '#description' => $this->t('Enables tabbing and arrow key navigation'),
    ];

    $form['views_slick_settings']['additional']['adaptiveHeight'] = [
      '#title' => $this->t('Adaptive Height'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['adaptiveHeight'] ?? NULL,
      '#description' => $this->t('Enables adaptive height for single slide horizontal carousels'),
    ];

    $form['views_slick_settings']['additional']['draggable'] = [
      '#title' => $this->t('Draggable'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['draggable'] ?? 1,
      '#description' => $this->t('Enable mouse dragging'),
    ];

    $form['views_slick_settings']['additional']['cssEase'] = [
      '#title' => $this->t('CSS Ease'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['additional']['cssEase'] ?? 'ease-in-out',
      '#description' => $this->t('CSS3 Animation Easing'),
    ];

    $form['views_slick_settings']['additional']['fade'] = [
      '#title' => $this->t('Fade'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['fade'] ?? NULL,
      '#description' => $this->t('Enable fade'),
    ];

    $form['views_slick_settings']['additional']['focusOnSelect'] = [
      '#title' => $this->t('focus On Select'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['focusOnSelect'] ?? NULL,
      '#description' => $this->t('Enable focus on selected element (click)'),
    ];

    $form['views_slick_settings']['additional']['easing'] = [
      '#title' => $this->t('Easing'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['additional']['easing'] ?? 'linear',
      '#description' => $this->t('Add easing for jQuery animate. Use with easing libraries or default easing methods'),
    ];

    $form['views_slick_settings']['additional']['edgeFriction'] = [
      '#title' => $this->t('Edge Friction'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['additional']['edgeFriction'] ?? '0.15',
      '#description' => $this->t('Resistance when swiping edges of non-infinite carousels'),
    ];

    $form['views_slick_settings']['additional']['pauseOnFocus'] = [
      '#title' => $this->t('Pause On Focus'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['pauseOnFocus'] ?? 1,
      '#description' => $this->t('Pause Autoplay On Focus'),
    ];

    $form['views_slick_settings']['additional']['pauseOnHover'] = [
      '#title' => $this->t('Pause On Hover'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['pauseOnHover'] ?? 1,
      '#description' => $this->t('Pause Autoplay On Hover'),
    ];

    $form['views_slick_settings']['additional']['pauseOnDotsHover'] = [
      '#title' => $this->t('Pause On Dots Hover'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['pauseOnDotsHover'] ?? 1,
      '#description' => $this->t('Pause Autoplay when a dot is hovered'),
    ];

    $form['views_slick_settings']['additional']['respondTo'] = [
      '#title' => $this->t('Respond To'),
      '#type' => 'radios',
      '#options' => [
        'window' => $this->t('window'),
        'slider' => $this->t('slider'),
        'min' => $this->t('min'),
      ],
      '#default_value' => $this->options['views_slick_settings']['additional']['respondTo'] ?? 'window',
      '#description' => $this->t("Width that responsive object responds to. Can be 'window', 'slider' or 'min' (the smaller of the two)"),
    ];

    $form['views_slick_settings']['additional']['rows'] = [
      '#title' => $this->t('Rows'),
      '#type' => 'number',
      '#default_value' => $this->options['views_slick_settings']['additional']['rows'] ?? '1',
      '#description' => $this->t('Setting this to more than 1 initializes grid mode. Use slidesPerRow to set how many slides should be in each row'),
    ];

    $form['views_slick_settings']['additional']['slidesPerRow'] = [
      '#title' => $this->t('Slides Per Row'),
      '#type' => 'number',
      '#default_value' => $this->options['views_slick_settings']['additional']['slidesPerRow'] ?? '1',
      '#description' => $this->t('With grid mode intialized via the rows option, this sets how many slides are in each grid row.'),
    ];

    $form['views_slick_settings']['additional']['swipe'] = [
      '#title' => $this->t('Swipe'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['swipe'] ?? 1,
      '#description' => $this->t('Enable swiping'),
    ];

    $form['views_slick_settings']['additional']['swipeToSlide'] = [
      '#title' => $this->t('Swipe To Slide'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['swipeToSlide'] ?? NULL,
      '#description' => $this->t('Allow users to drag or swipe directly to a slide irrespective of slidesToScroll'),
    ];

    $form['views_slick_settings']['additional']['touchMove'] = [
      '#title' => $this->t('Touch Move'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['touchMove'] ?? 1,
      '#description' => $this->t('Enable slide motion with touch'),
    ];

    $form['views_slick_settings']['additional']['touchThreshold'] = [
      '#title' => $this->t('Touch Threshold'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['additional']['touchThreshold'] ?? '5',
      '#description' => $this->t('To advance slides, the user must swipe a length of (1/touchThreshold) * the width of the slider'),
    ];

    $form['views_slick_settings']['additional']['useCSS'] = [
      '#title' => $this->t('Use CSS'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['useCSS'] ?? 1,
      '#description' => $this->t('Enable/Disable CSS Transitions'),
    ];

    $form['views_slick_settings']['additional']['useTransform'] = [
      '#title' => $this->t('Use Transform'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['useTransform'] ?? 1,
      '#description' => $this->t('Enable/Disable CSS Transforms'),
    ];

    $form['views_slick_settings']['additional']['vertical'] = [
      '#title' => $this->t('Vertical'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['vertical'] ?? NULL,
      '#description' => $this->t('Vertical slide mode'),
    ];

    $form['views_slick_settings']['additional']['verticalSwiping'] = [
      '#title' => $this->t('verticalSwiping'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['verticalSwiping'] ?? NULL,
      '#description' => $this->t('Vertical swipe mode'),
    ];

    $form['views_slick_settings']['additional']['rtl'] = [
      '#title' => $this->t('rtl'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['rtl'] ?? NULL,
      '#description' => $this->t("Change the slider's direction to become right-to-left"),
    ];

    $form['views_slick_settings']['additional']['waitForAnimate'] = [
      '#title' => $this->t('Wait For Animate'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['additional']['waitForAnimate'] ?? 1,
      '#description' => $this->t('Ignores requests to advance the slide while animating'),
    ];

    $form['views_slick_settings']['additional']['zIndex'] = [
      '#title' => $this->t('zIndex'),
      '#type' => 'textfield',
      '#default_value' => $this->options['views_slick_settings']['additional']['zIndex'] ?? '1000',
      '#description' => $this->t('Set the zIndex values for slides, useful for IE9 and lower'),
    ];
    // Animation model settings
    // get fields views added fields
    $fields = ['' => $this->t('None')];
    $fields += $this->displayHandler->getFieldLabels(TRUE);

    $form['views_slick_settings']['animationModel'] = [
      '#type' => 'details',
      '#title' => $this->t('Animation Model settings'),
      '#open' => FALSE,
    ];

    $form['views_slick_settings']['animationModel']['checkForAnimate'] = [
      '#title' => $this->t('checked For Animate'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['views_slick_settings']['animationModel']['checkForAnimate'] ?? 0,
      '#description' => $this->t('Checked for animation work advance the slide'),
      '#attributes' => [
        'class' => ['slick-make-animate'],
      ],
    ];
    $form['views_slick_settings']['animationModel']['modelView'] = [
      '#title' => $this->t('Model View Type'),
      '#type' => 'select',
      '#options' => ['default'=>'Default',
        'hero_banner' => 'Hero banner',
        'card' => 'card',
      ],
      '#attributes' => [
        'class' => ['make-viewmodel'],
      ],
      '#default_value' => $this->options['views_slick_settings']['animationModel']['modelView'] ?? 'default',
      '#description' => $this->t('We want make change model View type'),
    ];
    
    $form['views_slick_settings']['animationModel']['slickSlideImage'] = [
      '#type' => 'select',
      '#title' => $this->t('Slick Slide Image'),
      '#options' => $fields,
      '#default_value' => $this->options['views_slick_settings']['animationModel']['slickSlideImage'],
      '#description' => $this->t('Select the field that will be used for the Slick Slide Image.'),
      '#states' => [
        'disabled' => [
          ':input.make-viewmodel' => ['value' => 'default'],
        ],
        'required' => [
          ':input.make-viewmodel'=> [['value'=>'hero_banner'], ['value'=>'card']],
        ],
      ],
    ];

    $form['views_slick_settings']['animationModel']['imageAnimationType'] = [
      '#title' => $this->t('Image Animation Type'),
      '#type' => 'select',
      '#options' => [
                      'zoomInImage' => 'zoomIn',
                      'zoomOutImage' => 'zoomOut',
                      'animate__slideInLeft' => 'slideInLeft',
                      'animate__slideInRight' => 'slideInRight'],
      '#default_value' => $this->options['views_slick_settings']['animationModel']['imageAnimationType'] ?? 'zoomInImage',
      '#description' => $this->t('We want make change animation model type'),
      '#states' => [
        'disabled' => [
          ':input.slick-multiple-animate' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['views_slick_settings']['animationModel']['slickSlideTitle'] = [
      '#type' => 'select',
      '#title' => $this->t('Slick Slide Title'),
      '#options' => $fields,
      '#default_value' => $this->options['views_slick_settings']['animationModel']['slickSlideTitle'],
      '#description' => $this->t('Select the field that will be used for the Slick Slide Title.'),
      '#states' => [
        'disabled' => [
          ':input.make-viewmodel' => ['value' => 'default'],
        ],
        'required' => [
          ':input.make-viewmodel'=> [['value'=>'hero_banner'], ['value'=>'card']],
        ],
      ],
    ];
    $form['views_slick_settings']['animationModel']['titleAnimationType'] = [
      '#title' => $this->t('Heading Animation Type'),
      '#type' => 'select',
      '#options' => [ 'selecte_animation' => 'selecte animation',
                      'animate__fadeInLeft'=>'fadeInLeft',
                      'animate__fadeInUp'=>'fadeInUp',
                      'animate__fadeInDown' => 'fadeInDown',
                      'animate__fadeInRight' => 'fadeInRight',
                      'animate__lightSpeedInRight' => 'lightSpeedInRight',
                      'animate__lightSpeedInLeft' => 'lightSpeedInLeft',
                      'animate__zoomIn' => 'zoomIn',
                      'animate__slideInDown'=> 'slideInDown',
                      'animate__slideInLeft' => 'slideInLeft',
                      'animate__slideInUp' => 'slideInUp',
                      'animate__slideInRight' => 'slideInRight'],
      '#default_value' => $this->options['views_slick_settings']['animationModel']['headingAnimationType'] ?? 'animate__fadeInLeft',
      '#description' => $this->t('We want make change animation model type'),
      '#states' => [
        'disabled' => [
          ':input.slick-multiple-animate' => ['checked' => TRUE],
        ],
      ],
    ];
   
    $form['views_slick_settings']['animationModel']['slickSlideSubtitle'] = [
      '#type' => 'select',
      '#title' => $this->t('Slick Slide Subtitle'),
      '#options' =>$fields,
      '#default_value' => $this->options['views_slick_settings']['animationModel']['slickSlideSubtitle'] ?? "",
      '#description' => $this->t('Select the field that will be used for the Slick Slide Subtitle.'),
      '#states' => [
        'disabled' => [
          ':input.make-viewmodel' => ['value' => 'default'],
        ],
      ],
    ];
    $form['views_slick_settings']['animationModel']['subTitleAnimationType'] = [
      '#title' => $this->t('subTitle Animation Type'),
      '#type' => 'select',
      '#options' => [ 'selecte_animation' => 'selecte animation',
                      'animate__fadeInLeft'=>'fadeInLeft',
                      'animate__fadeInUp'=>'fadeInUp',
                      'animate__fadeInDown' => 'fadeInDown',
                      'animate__fadeInRight' => 'fadeInRight',
                      'animate__lightSpeedInRight' => 'lightSpeedInRight',
                      'animate__lightSpeedInLeft' => 'lightSpeedInLeft',
                      'animate__zoomIn' => 'zoomIn',
                      'animate__slideInDown'=> 'slideInDown',
                      'animate__slideInLeft' => 'slideInLeft',
                      'animate__slideInUp' => 'slideInUp',
                      'animate__slideInRight' => 'slideInRight'],
      '#default_value' => $this->options['views_slick_settings']['animationModel']['subTitleAnimationType'] ?? 'animate__fadeInLeft',
      '#description' => $this->t('We want make change animation model type'),
      '#states' => [
        'disabled' => [
          ':input.slick-multiple-animate' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['views_slick_settings']['animationModel']['slickSlideDescription'] = [
      '#type' => 'select',
      '#title' => $this->t('Slick Slide Description'),
      '#options' => $fields,
      '#default_value' => $this->options['views_slick_settings']['animationModel']['slickSlideDescription'],
      '#description' => $this->t('Select the field that will be used for the Slick Slide Description.'),
      '#states' => [
        'disabled' => [
          ':input.make-viewmodel' => ['value' => 'default'],
        ],
      ],
    ];
    $form['views_slick_settings']['animationModel']['descriptionAnimationType'] = [
      '#title' => $this->t('Description Animation Type'),
      '#type' => 'select',
      '#options' => [ 'selecte_animation' => 'selecte animation',
                      'animate__fadeInLeft'=>'fadeInLeft',
                      'animate__fadeInUp'=>'fadeInUp',
                      'animate__fadeInDown' => 'fadeInDown',
                      'animate__fadeInRight' => 'fadeInRight',
                      'animate__lightSpeedInRight' => 'lightSpeedInRight',
                      'animate__lightSpeedInLeft' => 'lightSpeedInLeft',
                      'animate__zoomIn' => 'zoomIn',
                      'animate__slideInDown'=> 'slideInDown',
                      'animate__slideInLeft' => 'slideInLeft',
                      'animate__slideInUp' => 'slideInUp',
                      'animate__slideInRight' => 'slideInRight'],
      '#default_value' => $this->options['views_slick_settings']['animationModel']['descriptionAnimationType'] ?? 'animate__fadeInLeft',
      '#description' => $this->t('We want make change animation model type'),
      '#states' => [
        'disabled' => [
          ':input.slick-multiple-animate' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['views_slick_settings']['animationModel']['slickSlideButton'] = [
      '#type' => 'select',
      '#title' => $this->t('Slick Slide Button'),
      '#options' => $fields,
      '#default_value' => $this->options['views_slick_settings']['animationModel']['slickSlideButton'],
      '#description' => $this->t('Select the field that will be used for the Slick Slide link to Button.'),
      '#states' => [
        'disabled' => [
          ':input.make-viewmodel' => ['value' => 'default'],
        ],
      ],
    ];

    $form['views_slick_settings']['animationModel']['rendomAnimate'] = [
      '#title' => $this->t('if select to multiple animate make Auto'),
      '#type' => 'checkbox',
      '#default_value' =>$this->options['views_slick_settings']['animationModel']['rendomAnimate'] ?? NULL,
      '#description' => $this->t('Rendom Animate when you selected multiple'),
      '#attributes' => [
        'class' => ['slick-multiple-animate'],
      ],
    ];

    $form['views_slick_settings']['animationModel']['multipleAnimates'] = [
      '#title' => $this->t('Animates multiple class select'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => [
      'animate__slideInDown'=> 'slideInDown',
      'animate__slideInLeft' => 'slideInLeft',
      'animate__slideInUp' => 'slideInUp to center',
      'animate__slideInRight' => 'slideInRight'],
      '#default_value' =>$this->options['views_slick_settings']['animationModel']['multipleAnimates'] ?? "animate__slideInLeft",
      '#description' => $this->t('Select whether you want multiple animations'),
       // Set the field as "read-only" when the "auto animations" is unchecked.
      '#states' => [
        'disabled' => [
          ':input.slick-multiple-animate' => ['checked' => FALSE],
        ],
      ],
    ];

  }
}