<?php

namespace Drupal\Tests\captcha\Kernel\Migrate\d7;

use Drupal\captcha\CaptchaPointInterface;
use Drupal\captcha\Entity\CaptchaPoint;
use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;

/**
 * Migrates the sessions owned by the captcha module.
 *
 * @group captcha
 */
class MigrateCaptchaSessionsTest extends MigrateDrupal7TestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['captcha'];

  protected $captchaSessions = [
    [
      'csid' => 1,
      'token' => '69e2767a2c651a887764bb60ea04cd0a',
      'uid' => 0,
      'sid' => 'svBxnT_AK4YFTbiUdCN3g9lCEqhC66NEbxasNNvGRug',
      'ip_address' => '172.18.0.1',
      'timestamp' => 1617948210,
      'form_id' => 'user_login_block',
      'solution' => '11',
      'status' => 0,
      'attempts' => 0,
    ],
    [
      'csid' => 2,
      'token' => '69e2767a2c651a887764bb60ea04cd0b',
      'uid' => 0,
      'sid' => 'avBxnT_AK4YFTbiUdCN3g9lCEqhC66NEbxasNNvGRug',
      'ip_address' => '172.18.0.1',
      'timestamp' => 1617948230,
      'form_id' => 'user_login_block',
      'solution' => '20',
      'status' => 0,
      'attempts' => 0,
    ],
    [
      'csid' => 3,
      'token' => '69e2767a2c651a887764bb60ea04cd0c',
      'uid' => 0,
      'sid' => 'bvBxnT_AK4YFTbiUdCN3g9lCEqhC66NEbxasNNvGRug',
      'ip_address' => '172.18.0.1',
      'timestamp' => 1617948240,
      'form_id' => 'user_login_block',
      'solution' => '25',
      'status' => 0,
      'attempts' => 0,
    ],
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->loadFixture(implode(DIRECTORY_SEPARATOR, [
      DRUPAL_ROOT,
      drupal_get_path('module', 'captcha'),
      'tests',
      'fixtures',
      'drupal7.php',
    ]));

    $this->installEntitySchema('captcha_point');
    $this->installSchema('captcha', ['captcha_sessions']);
    $this->installConfig('captcha');

    $migrations = [
      'd7_captcha_sessions',
    ];
    $this->executeMigrations($migrations);
  }

  /**
   * Tests that all expected sessions were migrated.
   */
  public function testCaptchaSessionsMigration() {
    // Test captcha points.
    foreach ($this->captchaSessions as $expected_captcha_session) {
      // Unfortunately captcha doesn't wrap the sessions around a service.
      // So a direct DB call has to be done here. *shrug*
      // Get the status of the current CAPTCHA session.
      $source_captcha_session = \Drupal::database()
        ->select('captcha_sessions', 'cs')
        ->fields('cs')
        ->condition('csid', $expected_captcha_session['csid'])
        ->execute()
        ->fetchAssoc();

      $this->assertEquals($source_captcha_session, $expected_captcha_session);
    }
  }

}
