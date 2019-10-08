<?php

/**
 * @file
 * Functionality and helper functions for CAPTCHA administration.
 */

/**
 * Helper function for generating an example challenge.
 */
function _captcha_generate_example_challenge($module, $type) {
  return [
    '#type' => 'captcha',
    '#captcha_type' => $module . '/' . $type,
    '#captcha_admin_mode' => TRUE,
  ];
}
