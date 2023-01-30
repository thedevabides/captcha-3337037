<?php

namespace Drupal\captcha_flood;

use Drupal\Core\Flood\FloodInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * CAPTCHA Flood Control service.
 *
 * @see: \Drupal\Core\Flood\DatabaseBackend
 */
class CaptchaFloodControl implements CaptchaFloodControlInterface {

  /**
   * The decorated flood service.
   *
   * @var \Drupal\Core\Flood\FloodInterface
   */
  protected $flood;

  /**
   * Event dispatcher.
   *
   * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Construct the CaptchaFloodControl.
   *
   * @param \Drupal\Core\Flood\FloodInterface $flood
   *   The flood service.
   * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack used to retrieve the current request.
   */
  public function __construct(FloodInterface $flood, EventDispatcherInterface $event_dispatcher, RequestStack $request_stack) {
    $this->flood = $flood;
    $this->eventDispatcher = $event_dispatcher;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public function isAllowed($name, $threshold, $window = 3600, $identifier = NULL) {
    if ($this->flood->isAllowed($name, $threshold, $window, $identifier)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function register($name, $window = 3600, $identifier = NULL) {
    return $this->flood->register($name, $window, $identifier);
  }

  /**
   * {@inheritdoc}
   */
  public function clear($name, $identifier = NULL) {
    return $this->flood->clear($name, $identifier);
  }

  /**
   * {@inheritdoc}
   */
  public function garbageCollection() {
    return $this->flood->garbageCollection();
  }

}
