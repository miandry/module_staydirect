<?php

namespace Drupal\booking_checkout\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class for Booking Step 3.
 */
class BookingStepThreeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'booking_step_three_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['review'] = [
      '#markup' => '<p>' . $this->t('Review your details and confirm booking.') . '</p>',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Confirm and Pay'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage($this->t('Booking confirmed and payment processed.'));
    // Redirect to a confirmation page or other action.
    $form_state->setRedirect('<front>');
  }
}
