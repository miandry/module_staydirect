<?php

namespace Drupal\maintenance_mode_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MaintenanceModeController extends ControllerBase {

  /**
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheTagsInvalidator;

  /**
   * Constructs a MaintenanceModeController object.
   *
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cache_tags_invalidator
   *   The cache tags invalidator service.
   */
  public function __construct(CacheTagsInvalidatorInterface $cache_tags_invalidator) {
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cache_tags.invalidator')
    );
  }

  /**
   * Toggles the maintenance mode.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response indicating the new status.
   */
  public function toggle(Request $request) {
    // Define your API key (this should ideally be stored in a secure way).
    $expected_api_key = '9E95781791DAD5712B9DC78E2C975';

    // Retrieve the API key from the request headers.
    $api_key = $request->query->get('API-Key');

    // Verify the API key.
    // if ($api_key !== $expected_api_key) {
    //   return new JsonResponse(['error' => 'Unauthorized access.'], Response::HTTP_FORBIDDEN);
    // }

    // Check current maintenance mode status.
    $current_status = \Drupal::state()->get('system.maintenance_mode');
   // var_dump( $current_status);die();
    // Toggle maintenance mode.
    $new_status = !$current_status;
    \Drupal::state()->set('system.maintenance_mode',  $new_status);
    // Clear all caches to apply changes immediately.
    $this->cacheTagsInvalidator->invalidateTags(['rendered']);

    // Return JSON response with the new status.
    return new JsonResponse([
      'status' => $new_status ? 'enabled' : 'disabled',
      'message' => $new_status ? 'Maintenance mode enabled and cache cleared.' : 'Maintenance mode disabled and cache cleared.',
    ]);
  }

  public function enabled(Request $request) {

  }
}
