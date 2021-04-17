<?php

namespace Drupal\Tests\captcha\Kernel\Migrate\d7;

use Drupal\captcha\CaptchaPointInterface;
use Drupal\captcha\Entity\CaptchaPoint;
use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;

/**
 * Migrates various configuration objects owned by the captcha module.
 *
 * @group captcha
 */
class MigrateCaptchaPointsTest extends MigrateDrupal7TestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['captcha'];

  protected $captchaPoints = [
    [
      'form_id' => 'comment_node_article_form',
      'captcha_type' => 'captcha/Math',
      'status' => TRUE,
    ],
    [
      'form_id' => 'user_pass',
      'captcha_type' => 'captcha/Math',
      'status' => TRUE,
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
      'd7_captcha_points',
    ];
    $this->executeMigrations($migrations);

    $this->captchaStorage = $this->container->get('entity_type.manager')
      ->getStorage('captcha_point');
  }

  /**
   * Tests a single captcha point type.
   *
   * @dataProvider testCaptchaPointDataProvider
   *
   * @param string $form_id
   *   The captcha point form id.
   * @param string $captcha_type
   *   The expected captcha type for the config.
   * @param $status
   */
  protected function assertEntity(string $form_id, string $captcha_type, $status) {
    /** @var \Drupal\captcha\CaptchaPointInterface $entity */
    $entity = CaptchaPoint::load($form_id);
    $this->assertInstanceOf(CaptchaPointInterface::class, $entity);
    $this->assertSame($form_id, $entity->label());
    $this->assertSame($captcha_type, $entity->getCaptchaType());
    $this->assertSame($status, $entity->status());
  }

  /**
   * Tests that all expected configuration gets migrated.
   */
  public function testCaptchaPointsMigration() {
    // Test captcha points.
    foreach ($this->captchaPoints as $captcha_point) {
      $this->assertEntity($captcha_point['form_id'], $captcha_point['captcha_type'], $captcha_point['status']);
    }
  }

}
