<?php

namespace Drupal\mz_staydirect;


use Drupal\Core\Database\Database;
use Drupal\user\Entity\User;

/**
 * Class DefaultService.
 */
class StayDirectService {
  /**
   * Constructs a new DefaultService object.
   */
  public function __construct() {
  }
  
  public function  checkString($str) {

    // Regex pattern to match strings starting with 'block_content_' and ending with '_edit_form'
    $pattern = '/^block_content_.*_edit_form$/';

    // Check if the string matches the pattern
    if (preg_match($pattern, $str)) {
        return true;
    } else {
        return false;
    }
  } 

  function checkDrupalRequiredTables($externalbd) {
    $required_tables = [
      'key_value', 'users', 'users_field_data', 'user__roles',
      'node', 'node_field_data', 'taxonomy_term_data'
    ];
    $missing_tables = [];
    try {
      // Get the connection to the target database.
      $connection = Database::getConnection('default', $externalbd);
      $schema = $connection->schema();
  
      // Check if each required table exists.
      foreach ($required_tables as $table) {
        if (!$schema->tableExists($table)) {
          $missing_tables[] = $table;
        }
      }
      // Return the results.
      if (empty($missing_tables)) {
        return false;
      } 
    } catch (\Exception $e) {
      $message = 'Error connecting to the database: ' . $e->getMessage() ;
      \Drupal::logger( 'mz_staydirect' )->error( $message );
     
    }
    return true ;
  }

  function update_username( $current_username, $new_username ) {
    // Load user by current username.
    $users = \Drupal::entityTypeManager()
    ->getStorage( 'user' )
    ->loadByProperties( [ 'name' => $current_username ] );

    // Ensure user exists.
    if ( $users ) {
        $user = reset( $users );
        // Check if the user is loaded and not anonymous.
        if ( $user instanceof User && !$user->isAnonymous() ) {
            // Update the username.
            $user->setUsername( $new_username );

            // Save the updated user.
            try {
                $user->save();
                \Drupal::logger( 'mz_staydirect' )->info( 'Username updated successfully.' );
            } catch ( \Exception $e ) {
                \Drupal::logger( 'mz_staydirect' )->error( 'Failed to update username: ' . $e->getMessage() );
            }
        } else {
            \Drupal::logger( 'mz_staydirect' )->warning( 'User could not be found or is anonymous.' );
        }
    } else {
        \Drupal::logger( 'mz_staydirect' )->warning( 'No user found with the given username.' );
    }
  }

  function deleteDemoUser(){
    $users = \Drupal::entityTypeManager()
    ->getStorage('user')
    ->loadByProperties(['name' => 'demo']);
    if (!empty($users)) {
        $user = reset($users); 
        $user->delete();
    } 
  }
  public function toggle_maintenance_mode($enable) {
    $state = \Drupal::state();
    if ($enable) {
      $state->set('system.maintenance_mode', TRUE);
      \Drupal::messenger()->addMessage('The site is now in maintenance mode.');
    } else {
      $state->set('system.maintenance_mode', FALSE);
      \Drupal::messenger()->addMessage('The site is no longer in maintenance mode.');
    }
    drupal_flush_all_caches();

  }
  public function executeJsonSite($site_name){
    $path = DRUPAL_ROOT.'/sites/default/files/sites/' ;
    $file = $site_name.'.json';
    $file_path =  $path. $file;

    if ( file_exists( $file_path ) ) {
        // Get the contents of the JSON file.
        $json_content = file_get_contents( $file_path );
        // Decode the JSON content into a PHP array.
        $data = json_decode( $json_content, TRUE );
        if($data["value"]["status"] == 0 && 
           \Drupal::state()->get('system.maintenance_mode') == 0){
          $this->toggle_maintenance_mode(TRUE);
        }
        if($data["value"]["status"] == 1 && 
            \Drupal::state()->get('system.maintenance_mode') == 1){
            $this->toggle_maintenance_mode(FALSE);
        }

    } else {
        \Drupal::logger( 'mz_staydirect' )->error( 'Failed  JsonConfig not exist in '.$file_path );

        return false ;

    }
}

}