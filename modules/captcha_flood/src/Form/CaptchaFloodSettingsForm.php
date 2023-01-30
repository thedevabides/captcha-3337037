<?php

namespace Drupal\captcha_flood\Form;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Datetime\DateFormatterInterface;

/**
 * Displays the pants settings form.
 */
class CaptchaFloodSettingsForm extends ConfigFormBase {

  const DEFAULT_IP_LIMIT = 50;
  const DEFAULT_IP_WINDOW = 3600;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Drupal\Core\Datetime\DateFormatterInterface definition.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a \Drupal\image_captcha\Form\ImageCaptchaSettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   *   The date.formatter service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LanguageManagerInterface $language_manager, DateFormatterInterface $dateFormatter) {
    parent::__construct($config_factory);
    $this->languageManager = $language_manager;
    $this->dateFormatter = $dateFormatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('language_manager'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'image_captcha_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['captcha_flood.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('captcha_flood.settings');

    // Enable/disable flood timeouts.
    $form['enable_captcha_flood'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enforce rate limiting for CAPTCHA'),
      '#description' => $this->t('Enable CAPTCHA flood timeouts by IP address.'),
      '#default_value' => $config->get('enable_captcha_flood'),
    ];

    // Configuration of Flood protection.
    $form['flood_protection'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Flood timeout settings'),
      // '#description' => $this->t("Configure flood protection for wrong attempts."),
      '#open' => TRUE,
      '#states' => [
        'disabled' => [
          ':input[name="enable_captcha_flood"]' => [ 'checked' => FALSE ],
        ],
      ],
    ];

    // Occurrence and durations config options.
    $occurrenceLimits = [
      1,
      2,
      3,
      4,
      5,
      6,
      7,
      8,
      9,
      10,
      20,
      30,
      40,
      50,
      75,
      100,
      125,
      150,
      200,
      250,
      500,
    ];
    $durationLimits = [
      60,
      180,
      300,
      600,
      900,
      1800,
      2700,
      3600,
      10800,
      21600,
      32400,
      43200,
      86400,
    ];

    $durationLimits_options = $this->buildOptions($durationLimits);
    $durationLimits_options[0] = $this->t('None (disabled)');
    ksort($durationLimits_options);

    $form['flood_protection']['ip_limit'] = [
      '#type'           => 'select',
      '#title'          => $this->t('Failed CAPTCHAs (IP) limit'),
      '#default_value'  => $config->get('ip_limit') ?? self::DEFAULT_IP_LIMIT,
      '#options'        => array_combine($occurrenceLimits, $occurrenceLimits),
    ];

    $form['flood_protection']['ip_window'] = [
      '#type'           => 'select',
      '#title'          => $this->t('Failed CAPTCHAs (IP) window'),
      '#default_value'  => $config->get('ip_window') ?? self::DEFAULT_IP_WINDOW,
      '#options'        => $durationLimits_options,
    ];

    // Field for the wrong captcha response error message.
    $form['flood_protection']['captcha_flood_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CAPTCHA flood response error message'),
      '#description' => $this->t('Configurable error message that the user gets when they exceed the CAPTCHA flood limit.'),
      '#default_value' => $this->getFloodErrorMessage(),
      '#maxlength' => 256,
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('captcha_flood.settings');
    $config->set('enable_captcha_flood', $form_state->getValue('enable_captcha_flood'));
    $config->set('ip_limit', $form_state->getValue('ip_limit'));
    $config->set('ip_window', $form_state->getValue('ip_window'));
    $config->set('captcha_flood_message', $form_state->getValue('captcha_flood_message'));
    $config->save();
    $this->messenger()->addStatus($this->t('The CAPTCHA flood settings have been saved.'));

    parent::submitForm($form, $form_state);
  }

  /**
   * Provide DateFormatter interval.
   *
   * @param array $time_intervals
   *   Intervals time array.
   * @param int $granularity
   *   Ganularity value.
   * @param string|null $langcode
   *   Langcode value.
   *
   * @return array
   *   Return an array.
   */
  protected function buildOptions(array $time_intervals, $granularity = 2, $langcode = NULL) {
    $callback = function ($value) use ($granularity, $langcode) {
      return $this->dateFormatter->formatInterval($value, $granularity, $langcode);
    };
    return array_combine($time_intervals, array_map($callback, $time_intervals));
  }

  /**
   * Gets the error message for when a user exceeds the CAPTCHA flood limit.
   *
   * @return string
   *   Error message.
   */
  protected function getFloodErrorMessage() {
    $error_message = $this->config('captcha_flood.settings')
      ->get('captcha_flood_message');
    if ($error_message) {
      return Xss::filter($error_message);
    }
    return $this->t('Too many attempts. Try again later.');
  }

}
