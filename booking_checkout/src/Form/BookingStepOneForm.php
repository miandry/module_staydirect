<?php

namespace Drupal\booking_checkout\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class for Booking Step 1.
 */
class BookingStepOneForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'booking_step_one_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['booking_options'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select a booking option'),
      '#options' => [
        'option_1' => $this->t('Option 1'),
        'option_2' => $this->t('Option 2'),
      ],
      '#required' => TRUE,
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next'),
    ];
    $form['#theme'] = 'checkout_step_one';

    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save the selected booking option and redirect to step 2.
    $selected_option = $form_state->getValue('booking_options');
    \Drupal::messenger()->addMessage($this->t('You selected @option', ['@option' => $selected_option]));
    $form_state->setRedirect('booking_checkout.step_2');
  }
}
