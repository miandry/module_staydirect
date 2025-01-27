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

}