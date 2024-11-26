<?php

namespace Drupal\mz_staydirect;


use Drupal\Core\Database\Database;

/**
 * Class DefaultService.
 */
class StayDirectService {
  /**
   * Constructs a new DefaultService object.
   */
  public function __construct() {
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

}