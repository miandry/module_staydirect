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
    \Drupal::logger('mz_staydirect')->notice('Ceci est un message d\'information.');

    if($id == null ){
      $base_url = \Drupal::request()->getSchemeAndHttpHost();
      $url =    $base_url.'/user';
      $response = new RedirectResponse($url);
      $response->send();
    }
    $form['id'] = [
        '#type' => 'hidden',
        '#value' => $id,
    ];
    $body =  $this->include_template("unsubscribe-content.html.twig",[]) ;
    $form['text_unsubscribe_1'] = [
      '#markup' => '<div class="unsubscribe_content container"><div class="row"><div class="col">',
    ];
    $form['text_unsubscribe_2'] = [
      '#markup' => $body,
    ];
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Unsubscribe'),
        '#attributes' => [
          'class' => ['btn-primary'],
        ],
      ];
      $form['text_unsubscribe_3'] = [
        '#markup' =>'</div></div></div>',
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
      kint($site);die();
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

          $subscriptionId = $params_site['subscriptionId'];
          $service = \Drupal::service('mz_payment.manager');
          $result = $service->unSubscription($subscriptionId);
          if($result){
            $message = 'You have sucessfully unSubscribe in STRIPE with id='.$subscriptionId;
            \Drupal::logger('mz_staydirect')->notice($message);
              

            // $base_url = \Drupal::request()->getSchemeAndHttpHost();
            // $url =    $base_url.'/user';
            // $response = new RedirectResponse($url);
            // $response->send();

          }else{
            $message = 'Failed unSubscribe in STRIPE with id='.$subscriptionId;
            \Drupal::logger('mz_staydirect')->error($message);
          }
      

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
