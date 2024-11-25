<?php

namespace Drupal\booking_checkout\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\booking_checkout\Form\BookingStepOneForm;
use Drupal\booking_checkout\Form\BookingStepTwoForm;
use Drupal\booking_checkout\Form\BookingStepThreeForm;

/**
 * Provides the checkout process for bookings.
 */
class CheckoutController extends ControllerBase {

  /**
   * Step 1: Select Booking.
   */
  public function stepOne() {
    $form = \Drupal::formBuilder()->getForm(BookingStepOneForm::class);
    return [
      '#title' => 'Step 1: Select Booking',
      'form' => $form,
    ];
  }

  /**
   * Step 2: Enter Details.
   */
  public function stepTwo() {
    $form = \Drupal::formBuilder()->getForm(BookingStepTwoForm::class);
    return [
      '#title' => 'Step 2: Enter Details',
      'form' => $form,
    ];
  }

  /**
   * Step 3: Review and Pay.
   */
  public function stepThree() {
    $form = \Drupal::formBuilder()->getForm(BookingStepThreeForm::class);
    return [
      '#title' => 'Step 3: Review and Pay',
      'form' => $form,
    ];
  }
}
