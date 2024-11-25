<?php

namespace Drupal\booking_checkout\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class for Booking Step 2.
 */
class BookingStepTwoForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'booking_step_two_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your Email'),
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save the user details and redirect to step 3.
    $name = $form_state->getValue('name');
    $email = $form_state->getValue('email');
    \Drupal::messenger()->addMessage($this->t('Details saved: @name, @email', ['@name' => $name, '@email' => $email]));
    $form_state->setRedirect('booking_checkout.step_3');
  }
}
