<?php

/**
 * @file
 * Contains \Drupal\geo_redirect\Form\GeoRedirectDeleteForm.
 */

namespace Drupal\geo_redirect\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class GeoRedirectDeleteForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'geo_redirect_delete_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $gid = NULL) {
    $geo_redirect = geo_redirect_load($gid);

    $form['gid'] = ['#type' => 'value', '#value' => $gid];
    $countries = geo_redirect_country_names();
    return confirm_form($form, t('Are you sure you want to delete redirect URL for <em>%country</em>?', [
      '%country' => $countries[$geo_redirect->country_code]
      ]), 'admin/config/search/geo-redirect/list');
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    geo_redirect_delete($form_state->getValue(['gid']));
    drupal_set_message(t('The geo redirect has been deleted.'));
    $form_state->set(['redirect'], 'admin/config/search/geo-redirect/list');
  }

}
?>
