<?php

namespace Drupal\mz_staydirect\Form;


use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * Edit config variable form.
 */
 
class unsubscribeForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'unsubscribe_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $config_name = '')
  { $id = \Drupal::request()->query->get('id');
 
    if($id == null ){
      $base_url = \Drupal::request()->getSchemeAndHttpHost();
      $url =    $base_url.'/user';
      $response = new RedirectResponse($url);
      $response->send();
    }
    $form['#theme'] = 'unsubscribe_theme';
    $form['id'] = [
        '#type' => 'hidden',
        '#value' => $id,
    ];
    $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Unsubscribe'),
        '#attributes' => [
          'class' => ['btn-custom'],
        ],
    ];
 
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  { 
    $values = $form_state->getValues();
    if(isset($values["id"])){
      $id = $values["id"];
      $site = \Drupal::entityTypeManager()->getStorage('node')->load($id);
      $message = 'Failed to unSubscribe';
      $status = false ;
      if(is_object($site)){
        $current_user = \Drupal::currentUser();
        $current_user_id =  $current_user->id();
        $node_author_id = $site->getOwnerId();
        $roles = $current_user->getRoles();
        if($current_user_id === $node_author_id || 
          in_array('admin',  $roles) || 
          in_array('webmaster',  $roles)) {
          $service = \Drupal::service('mz_payment.manager');
          $result = $service->executeUnSubscription($site);
          if($result){
            $message = 'You have sucessfully unSubscribe ';
            \Drupal::messenger()->addMessage($message);
          }else{
            $message = 'Failed unSubscribe in STRIPE with id='.$subscriptionId;
            \Drupal::logger('mz_staydirect')->error($message);
          }
          $base_url = \Drupal::request()->getSchemeAndHttpHost();
          $url =    $base_url.'/user';
          $response = new RedirectResponse($url);
          $response->send();
      
        } else {

          $message = 'You dont have permission  to unSubscribe !! , please the website admin';
          $status = false ;

        }
    
      }
    }
  }
  private  function include_template($id,$var){
    $service = \Drupal::service('templating.manager');
    if(is_numeric($id)){
        $template= $service->getTemplatingById($id);
    }else{
        $template= $service->getTemplatingByTitle($id);
    }
    if(is_object($template)){
        $output = $template->field_templating_html->value;
        $template_ouput =  [
            '#type' => 'inline_template',
            '#template' => $output,
            'status' => true ,
            '#context' => [
              'var' => $var,
            ],
          ];
        
    }else{
        $output = "<b>Template custom not find</b>";
        $template_ouput =  [
            '#type' => 'inline_template',
            '#template' => $output,
            'status' => false,
            '#context' => [
              'var' => $var,
            ],
          ];
    }
    return \Drupal::service('renderer')->render( $template_ouput);
 
}
}
