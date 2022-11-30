<?php

namespace Drupal\Tests\image_captcha\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * This class provides methods specifically for testing basic functionalities.
 *
 * @group image_captcha
 */
class ImageCaptchaBasicFunctionalTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'test_page_test',
    'captcha',
    'image_captcha',
  ];

  /**
   * A user with authenticated permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * A user with admin permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->config('system.site')->set('page.front', '/test-page')->save();
    $this->user = $this->drupalCreateUser([]);
    $this->adminUser = $this->drupalCreateUser([]);
    $this->adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests if the image captcha settings page is accessible.
   */
  public function testImageCaptchaSettingsPage() {
    $session = $this->assertSession();
    $this->drupalGet('admin/config/people/captcha/image_captcha');
    $session->statusCodeEquals(200);
    $session->pageTextContains('Example');
    $session->pageTextContains('Color and image settings');
  }

}
