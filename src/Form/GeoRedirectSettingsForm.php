<?php

/**
 * @file
 * Contains \Drupal\geo_redirect\Form\GeoRedirectSettingsForm.
 */

namespace Drupal\geo_redirect\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class GeoRedirectSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'geo_redirect_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('geo_redirect.settings');

    foreach (Element::children($form) as $variable) {
      $config->set($variable, $form_state->getValue($form[$variable]['#parents']));
    }
    $config->save();

    if (method_exists($this, '_submitForm')) {
      $this->_submitForm($form, $form_state);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['geo_redirect.settings'];
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form = [];
    // Enable geo redirect.
    $form['geo_redirect_enabled'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable geo redirect'),
      '#default_value' => variable_get('geo_redirect_enabled', TRUE),
      '#description' => t('Check this box if you want to enable geo redirection. Uncheck to disable it.'),
    ];
    // Log redirect entries.
    $form['geo_redirect_log'] = [
      '#type' => 'checkbox',
      '#title' => t('Log geo redirections'),
      '#default_value' => variable_get('geo_redirect_log', FALSE),
      '#description' => t('Log entry when user is redirected.'),
    ];
    // Allow debugging.
    $form['geo_redirect_debug'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable debugging'),
      '#default_value' => variable_get('geo_redirect_debug', FALSE),
      '#description' => t('Enable debugging for Geo Redirect. You can specify IP address with URL to test with. E.g. http://yoursite.com/?grip_debug=86.30.200.145'),
    ];
    // User roles.
    $form['geo_redirect_user_roles'] = [
      '#type' => 'checkboxes',
      '#title' => t('Roles'),
      '#options' => user_roles(),
      '#default_value' => variable_get('geo_redirect_user_roles', [
        DRUPAL_ANONYMOUS_RID => DRUPAL_ANONYMOUS_RID
        ]),
      '#description' => t('Select user roles for which redirection is enabled.'),
    ];
    return parent::buildForm($form, $form_state);
  }

}
?>
