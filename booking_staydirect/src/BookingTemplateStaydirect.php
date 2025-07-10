<?php

namespace Drupal\booking_staydirect;


use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;


/**
 * Class DefaultService.
 */
class BookingTemplateStaydirect {
  /**
   * Constructs a new DefaultService object.
   */
  public function __construct() {
  }
  public  function cancelBooking($booking_id){
    $entity = \Drupal::service('entity_type.manager')->getStorage('node')->load($booking_id);
    $config = \Drupal::config('stripe.settings');
    $apikeySecret = $config->get('apikey.' . $config->get('environment') . '.secret');
    \Stripe\Stripe::setApiKey($apikeySecret);
      try {

        $paymentIntentId =  $entity->field_payment_intent_id->value ; // the ID of the PaymentIntent
        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
        if ($paymentIntent->status === 'requires_capture') {
              $paymentIntent->cancel();
              \Drupal::logger('booking_staydirect')->notice( "Canceled PaymentIntent: " . $paymentIntent->id);
        }
        if($paymentIntent->status === 'canceled'){
            $entity->field_status->value ='cancel' ;
            $entity->moderation_state->value = 'canceled';
            $prix = $entity->field_price_with_tax->value ;
            $entity->save();
            \Drupal::messenger()->addMessage('✅ Order cancelled successfully. Your payment of '.$prix.' USD has been cancelled.');
            return true;
        }
        return false ;


      } catch (\Stripe\Exception\ApiErrorException $e) {
        \Drupal::logger("booking_staydirect")->error( "Error: " . $e->getMessage());
         return false;
      }
  }
  function _get_notification_set(){
  
    // Build entity query.
    $query = \Drupal::entityQuery('node')
    ->condition('type', 'notification')
    ->condition('status', 1) // 1 = Published
    ->sort('created', 'DESC'); // Optional: sort by newest first

    // Execute the query to get node IDs.
    $nids = $query->execute();

    // Load full node entities.
    $nodes = Node::loadMultiple($nids);
    $resultats =[];
    foreach ($nodes as $node) {
        $resultats[$node->id()] = [
            'remiders' => -90
        ];
    }
    return  $resultats  ;

  }   
  function __reminderEmail(){
    $reminders = $this->_get_notification_set();
    foreach ($reminders  as $key => $r) {
    // Get the current date and time (UTC)
    $current_date = new DrupalDateTime();
    $current_date->setTimezone(new \DateTimeZone('UTC'));
      // Calculate the date 2 days from now
      $two_days_from_now = clone $current_date;
      $two_days_from_now->modify($r.' days');

    // Convert the dates to database format
    $two_days_from_now_db = $two_days_from_now->format('Y-m-d');
    // Build the Node Query
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'booking')
      ->condition('field_dates.value', '%'.$two_days_from_now_db.'%', 'LIKE')
      ->execute();
      if (!empty($query)) {
        foreach ($query as $booking_id) {
              $this->sendEmailRemind($key,$booking_id);
        }
      } else {
        \Drupal::logger('mz_booking')->notice('No bookings found within the next '+ $two_days_from_now +' days.');
      }
   } 
  }
  function sendEmailRemind($key,$booking_id){
    $parser = \Drupal::service('entity_parser.manager');
    $note = $parser->node_parser($key);
    $entity = \Drupal::service('entity_type.manager')->getStorage('node')->load($booking_id);
    $subject   = $note['subject'];
    $body   = $note['email_content'];
    $uid = $entity->getOwnerId();
    $item_user = \Drupal\user\Entity\User::load($uid);
    $to  = $item_user->getEmail();
    \Drupal::service('mz_message.default')->send_mail_simple($body,$to,$subject);
  }
  public  function formatEmailRemind($entity,$body,$subject){
    $service = \Drupal::service('mz_payment.manager');
    $token_service = \Drupal::token();
    $subject = $service->tokenCustomBooking($entity,$subject);
    $subject  = $token_service->replace($subject ,  ['node'=>$entity]);
    $site_name = \Drupal::config('system.site')->get('name');
    $subject   = str_replace('[site:name]',    $site_name  , $subject  );

    $body = $service->tokenCustomBooking($entity,$body);
    $body  = $token_service->replace($body ,  ['node'=>$entity]);
    $site_name = \Drupal::config('system.site')->get('name');
    $body   = str_replace('[site:name]',    $site_name  , $body  );

    $uid = $entity->getOwnerId();
    $item_user = \Drupal\user\Entity\User::load($uid);
    $to  = $item_user->getEmail();
    \Drupal::service('mz_message.default')->send_mail_simple($body,$to,$subject);
  }
}