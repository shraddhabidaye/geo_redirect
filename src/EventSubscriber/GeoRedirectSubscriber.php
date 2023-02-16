<?php /**
 * @file
 * Contains \Drupal\geo_redirect\EventSubscriber\InitSubscriber.
 */

namespace Drupal\geo_redirect\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GeoRedirectSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::REQUEST => ['onEvent', 0]];
  }

  public function onEvent() {
    // Only execute if redirection is enabled or this hook is
  // not called from drush. Also do not execute for admin pages.
    if (!variable_get('geo_redirect_enabled', TRUE) || function_exists('drush_main') || path_is_admin(current_path())) {
      return;
    }

    // Get country code for user.
    $country_code = geo_redirect_geoip_country_code();
    // Check if there is geo redirect is added for country.
    if ($geo_redirect = geo_redirect_load_by_country_code($country_code)) {

      $redirect = FALSE;

      global $user;
      $roles = array_keys($user->roles);
      foreach (variable_get('geo_redirect_user_roles', [
        DRUPAL_ANONYMOUS_RID => DRUPAL_ANONYMOUS_RID
        ]) as $rid) {
        if (!empty($rid) && in_array($rid, $roles)) {
          $redirect = TRUE;
          break;
        }
      }

      // Execute geo redirect.
      geo_redirect_execute($geo_redirect, $redirect);
    }
  }

}
