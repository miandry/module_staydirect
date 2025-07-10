<?php

namespace Drupal\booking_staydirect\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
/**
 * Class ApiController.
 */
class DefaultController extends ControllerBase {

  /**
   * Paragraph_delete.
   *
   * @return string
   *   Return Hello string.
   */
  public function cancel($id){
    $service_manage = \Drupal::service('booking_staydirect.manage');
    $service_manage->cancelBooking($id);
    $path = "/user" ;
    $response = new RedirectResponse($path, 302);
    $response->send();
    return;
 
  }

}
