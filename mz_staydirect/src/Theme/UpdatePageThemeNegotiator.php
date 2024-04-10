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
    if (
      $route_match->getRouteName() == 'system.db_update'
    ) {
         return "staydirect_install";
      }

      return FALSE;
  }

  /**
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   * @return null|string
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    return $this->negotiateRoute($route_match) ?: NULL;
  }
  private function negotiateRoute(RouteMatchInterface $route_match) {
    if (
        $route_match->getRouteName() == 'system.db_update'
      ) {
      return "staydirect_install";
    }

    return FALSE;
  }

}
