<?php

namespace Drupal\captcha\Service;

/**
 * Helper service for CAPTCHA module.
 */
class CaptchaService {

  /**
   * Return an array with the available CAPTCHA types.
   *
   * For use as options array for a select form elements.
   *
   * @param bool $add_special_options
   *   If true: also add the 'default' option.
   *
   * @return array
   *   An associative array mapping "$module/$type" to
   *   "$type (from module $module)" with $module the module name
   *   implementing the CAPTCHA and $type the name of the CAPTCHA type.
   */
  public function getAvailableChallengeTypes($add_special_options = TRUE) {
    $challenges = [];

    if ($add_special_options) {
      $challenges['default'] = t('Default challenge type');
    }

    // We do our own version of Drupal's module_invoke_all() here because
    // we want to build an array with custom keys and values.
    foreach (\Drupal::moduleHandler()->getImplementations('captcha') as $module) {
      $result = call_user_func_array($module . '_captcha', ['list']);
      if (is_array($result)) {
        foreach ($result as $type) {
          $challenges["$module/$type"] = t('@type (from module @module)', [
            '@type' => $type,
            '@module' => $module,
          ]);
        }
      }
    }

    return $challenges;
  }
}
