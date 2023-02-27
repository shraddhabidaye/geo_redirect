<?php /**
 * @file
 * Contains \Drupal\geo_redirect\Controller\DefaultController.
 */

namespace Drupal\geo_redirect\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Default controller for the geo_redirect module.
 */
class GeoRedirectController extends ControllerBase {

  public function geo_redirect_list() {
    $output = '';

    $destination = drupal_get_destination();

    $result = db_select('geo_redirect_list', 'gr')
      ->fields('gr', ['gid', 'country_code', 'redirect_url'])
      ->execute();

    $rows = [];
    $header = ['Country', 'Redirect URL', 'Operations'];
    $country_names = geo_redirect_country_names();
    foreach ($result as $item) {
      $row = [];
      $row[] = ['data' => $country_names[$item->country_code]];
      $row[] = ['data' => l($item->redirect_url, $item->redirect_url)];

      $operations = [];
      $operations['edit'] = [
        'title' => t('Edit'),
        'href' => 'admin/config/search/geo-redirect/edit/' . $item->gid,
        'query' => $destination,
      ];
      $operations['delete'] = [
        'title' => t('Delete'),
        'href' => 'admin/config/search/geo-redirect/delete/' . $item->gid,
        'query' => $destination,
      ];
      $row['operations'] = [
        'data' => [
          '#theme' => 'links',
          '#links' => $operations,
          '#attributes' => [
            'class' => [
              'links',
              'inline',
              'nowrap',
            ]
            ],
        ]
        ];
      $rows[] = $row;
    }
    $output .= theme('table', [
      'header' => $header,
      'rows' => $rows,
      'empty' => t('No geo redirect URLs available.'),
    ]);
    return $output;
  }

}