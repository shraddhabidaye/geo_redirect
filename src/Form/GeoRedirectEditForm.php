<?php

/**
 * @file
 * Contains \Drupal\geo_redirect\Form\GeoRedirectEditForm.
 */

namespace Drupal\geo_redirect\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class GeoRedirectEditForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'geo_redirect_edit_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $gid = NULL) {

    if (isset($gid)) {
      $geo_redirect = geo_redirect_load($gid);
      if (!($geo_redirect)) {
        drupal_not_found();
        exit();
      }
    }
    else {
      $geo_redirect = new stdClass();
      // Merge default values for geo redirect object.
      geo_redirect_object_prepare($geo_redirect);
    }

    $form['gid'] = ['#type' => 'hidden', '#value' => $geo_redirect->gid];
    $form['is_new'] = [
      '#type' => 'hidden',
      '#value' => empty($geo_redirect->gid),
    ];

    $form['country_code'] = [
      '#type' => 'select',
      '#title' => t('Select country'),
      '#options' => geo_redirect_country_names(),
      '#description' => t('Select the country for which you want the site to be redirected.'),
      '#required' => TRUE,
      '#default_value' => $geo_redirect->country_code,
    ];
    $form['redirect_url'] = [
      '#type' => 'textfield',
      '#title' => t('Enter URL'),
      '#required' => TRUE,
      '#description' => t('Enter the URL where you want to redirect the user from above country e.g. (@example).', [
        '@example' => 'http://www.example.com'
        ]),
      '#required' => TRUE,
      '#default_value' => $geo_redirect->redirect_url,
    ];
    $form['is_user_allowed'] = [
      '#type' => 'checkbox',
      '#title' => t('Allow <em>@user</em> path.', [
        '@user' => '/user'
        ]),
      '#description' => t('<p>If you check this box, user from above country can access the path <em>/user</em>.</p> <p><strong>It is strongly recommanded that you should check this box, if you are trying to redirect users from your country.</strong></p>'),
      '#default_value' => $geo_redirect->is_user_allowed,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
    ];
    $form['#validate'][] = 'geo_redirect_edit_form_validate';
    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $geo_redirect_url = $form_state->getValue(['redirect_url']);
    if (!valid_url($geo_redirect_url, TRUE)) {
      $form_state->setErrorByName('redirect_url', t('%url is not valid url.', [
        '%url' => $geo_redirect_url
        ]));
    }
    // Do not allow to add redirect for same country (only for new redirects).
    if ($form_state->getValue([
      'is_new'
      ])) {
      $country_code = $form_state->getValue(['country_code']);
      if ($exists = geo_redirect_load_by_country_code($country_code)) {
        $countries = geo_redirect_country_names();
        $form_state->setErrorByName('country_code', t('Redirect for %country already exists.', [
          '%country' => $countries[$country_code]
          ]));
      }
    }
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    form_state_values_clean($form_state);
    $geo_redirect = (object) $form_state->getValues();
    geo_redirect_save($geo_redirect);
    $form_state->set(['redirect'], 'admin/config/search/geo-redirect');
    drupal_set_message(t('Geo redirect has been saved successfully.'), 'status');
  }

}
?>
