<?php

require_once 'batchthresholdbundling.civix.php';
use CRM_Batchthresholdbundling_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function batchthresholdbundling_civicrm_config(&$config) {
  _batchthresholdbundling_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function batchthresholdbundling_civicrm_xmlMenu(&$files) {
  _batchthresholdbundling_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_alterSettingsMetaData().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsMetaData
 *
 */
function batchthresholdbundling_civicrm_alterSettingsMetaData(&$settingsMetadata, $domainID, $profile) {
  $settingsMetadata['threshold_bundling_amount'] = array(
    'group_name' => 'Contribute Preferences',
    'group' => 'contribute',
    'name' => 'threshold_bundling_amount',
    'quick_form_type' => 'Element',
    'type' => 'String',
    'html_type' => 'text',
    'default' => 1000.00,
    'add' => '5.6',
    'title' => 'Contribution threshold amount for bundling',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'help_text' => '',
  );
}

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
 */
function batchthresholdbundling_civicrm_preProcess($formName, &$form) {
  if ($formName == 'CRM_Admin_Form_Preferences_Contribute') {
    $settings = $form->getVar('_settings');
    $contributeSettings = array();
    foreach ($settings as $key => $setting) {
      $contributeSettings[$key] = $setting;
      if ($key == 'always_post_to_accounts_receivable') {
        $contributeSettings['threshold_bundling_amount'] = CRM_Core_BAO_Setting::CONTRIBUTE_PREFERENCES_NAME;
      }
    }
    $form->setVar('_settings', $contributeSettings);
  }
}

function batchthresholdbundling_civicrm_batchItems(&$queryResults, &$financialItems) {
  $thresholdAmount = CRM_Contribute_BAO_Contribution::checkContributeSettings('threshold_bundling_amount') ?: Civi::settings()->get('threshold_bundling_amount');
  if ($thresholdAmount > 0) {
    if (!empty($financialItems['ENTRIES'])) {
      $entries = $financialItems['ENTRIES'];
      $accountCollection = CRM_Utils_Array::collect('ACCOUNTID', $entries);
      // $totalAmounts stores total amount of each FAs
      // $unsetIDs store entries IDs that are bundled up into one and need to be deleted
      $totalAmounts = $unsetIDs = [];
      foreach ($accountCollection as $id => $account) {
        if ($entries[$id]['CONTRIBUTION_AMOUNT'] <= $thresholdAmount) {
          $unsetIDs[$account][] = $id;
          $totalAmounts[$account] = empty($totalAmounts[$account]) ? $entries[$id]['AMOUNT'] : ($totalAmounts[$account] + $entries[$id]['AMOUNT']);
        }
        unset($entries[$id]['CONTRIBUTION_AMOUNT']);
        unset($financialItems['ENTRIES'][$id]['CONTRIBUTION_AMOUNT']);
      }
      foreach ($totalAmounts as $account => $amountTotal) {
        $key = end($unsetIDs[$account]);
        $description = [];
        $finalEntry = array_merge($entries[$key], ['AMOUNT' => CRM_Contribute_BAO_Contribution_Utils::formatAmount($amountTotal)]);
        foreach ($unsetIDs[$account] as $id) {
          $description[] = $financialItems['ENTRIES'][$id]['DESCRIPTION'];
          unset($financialItems['ENTRIES'][$id]);
        }
        $description = implode(', ', array_unique($description));
        $financialItems['ENTRIES'][] = array_merge($finalEntry, ['DESCRIPTION' => $description]);
      }
    }
  }
}
/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function batchthresholdbundling_civicrm_install() {
  _batchthresholdbundling_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function batchthresholdbundling_civicrm_postInstall() {
  _batchthresholdbundling_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function batchthresholdbundling_civicrm_uninstall() {
  _batchthresholdbundling_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function batchthresholdbundling_civicrm_enable() {
  _batchthresholdbundling_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function batchthresholdbundling_civicrm_disable() {
  _batchthresholdbundling_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function batchthresholdbundling_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _batchthresholdbundling_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function batchthresholdbundling_civicrm_managed(&$entities) {
  _batchthresholdbundling_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function batchthresholdbundling_civicrm_caseTypes(&$caseTypes) {
  _batchthresholdbundling_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function batchthresholdbundling_civicrm_angularModules(&$angularModules) {
  _batchthresholdbundling_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function batchthresholdbundling_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _batchthresholdbundling_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function batchthresholdbundling_civicrm_entityTypes(&$entityTypes) {
  _batchthresholdbundling_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function batchthresholdbundling_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function batchthresholdbundling_civicrm_navigationMenu(&$menu) {
  _batchthresholdbundling_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _batchthresholdbundling_civix_navigationMenu($menu);
} // */
