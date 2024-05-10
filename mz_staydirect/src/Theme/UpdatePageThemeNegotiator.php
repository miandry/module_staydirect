<?php

namespace Drupal\mz_staydirect\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;


/**
 * Theme negotiator for the /update.php page.
 */
class UpdatePageThemeNegotiator implements ThemeNegotiatorInterface {


  public function __construct() {

  }
   
    /**
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   * @return bool
   */
  public function applies(RouteMatchInterface $route_match) {
    $route = $route_match->getRouteObject();
    $is_admin_route = \Drupal::service('router.admin_context')->isAdminRoute($route);
    global $site_variables ;
    $status = \Drupal::state()->get('system.maintenance_mode');
    if(!$is_admin_route && $site_variables  && isset($site_variables["site_theme"])){
       return TRUE ;
    }
    if( $status == 1){
      return TRUE ;
    }
    return FALSE;
  }

  /**
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   * @return null|string
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    global $site_variables;
    $theme = "staydirect_".$site_variables["site_theme"];
    // $status = \Drupal::state()->get('system.maintenance_mode');
    // if( $status == 1){
    //   $theme = "claro";
    // }
    
    return $theme ;
  }
  private function negotiateRoute(RouteMatchInterface $route_match) {
    return FALSE;
  }
}
