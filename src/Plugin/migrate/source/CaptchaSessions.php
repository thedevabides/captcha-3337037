<?php

namespace Drupal\captcha\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 7 captcha sessions from database.
 *
 * @MigrateSource(
 *   id = "d7_captcha_sessions",
 *   source_module = "captcha"
 * )
 */
class CaptchaSessions extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('captcha_sessions', 'c')->fields('c');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'csid' => $this->t('CAPTCHA session ID.'),
      'token' => $this->t('One time CAPTCHA token.'),
      'uid' => $this->t("User's {users}.uid."),
      'sid' => $this->t("Session ID of the user."),
      'ip_address' => $this->t('IP address of the visitor.'),
      'timestamp' => $this->t('A Unix timestamp indicating when the challenge was generated.'),
      'form_id' => $this->t('The form_id of the form where the CAPTCHA is added to.'),
      'solution' => $this->t('Solution of the challenge.'),
      'status' => $this->t('Status of the CAPTCHA session (unsolved, solved, ...)'),
      'attempts' => $this->t('The number of attempts.'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['csid']['type'] = 'integer';
    return $ids;
  }
}