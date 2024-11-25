<?php

namespace Drupal\mz_staydirect\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Drupal\Core\Url;

class UserLoginRedirectSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['onRespond', 0];
    return $events;
  }

  public function onRespond(ResponseEvent $event) {
    // $request = $event->getRequest();
    // $current_route_name = \Drupal::routeMatch()->getRouteName();
    // // Check if the current route is the user login form route.
    // if ($current_route_name == 'user.login') {
    //   // Check if the user is logged in.
    //   $query_params = $request->query->all();
    //   if (\Drupal::currentUser()->isAuthenticated() &&  isset($query_params["step"]) && $query_params["step"]=="/admin/bank/setup") {
    //     // Redirect to the user edit form.
    //     \Drupal::messenger()->addMessage(t('Please change your default password'));
    //     $uid = \Drupal::currentUser()->id();
    //     $url = Url::fromRoute('entity.user.edit_form', ['user' => $uid])->toString();
    //     $response = new RedirectResponse($url);
    //     $event->setResponse($response);
    //   }
    // }
  }
}
