<?php


namespace Drupal\mz_staydirect\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\user\Entity\User;
use Drupal\Core\Session\AccountInterface;
/**
 * Redirects users before HTML loading.
 */
class RedirectSubscriber implements EventSubscriberInterface {

  /**
   * Redirects users to a specific URL before HTML loading.
   */
  public function checkForRedirect(RequestEvent $event) {

    // Check if the request path matches the condition for redirection.
    if ($event->getRequest()->getRequestUri() === '/user/login') {
          
    }
   
    
  }


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Subscribe to the request event with a high priority.
    return [
      KernelEvents::REQUEST => ['checkForRedirect', 100],
    ];
  }


}
