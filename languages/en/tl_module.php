<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   FacebookConnect
 * @author    Mark Sturm
 * @author    Richard Henkenjohann
 * @copyright Mark Sturm 2013
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_module']['fb_settings_legend']	= 'Module settings';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['fb_changeFeMessage']                  = array('Custom button text for "Login with Facebook"');
$GLOBALS['TL_LANG']['tl_module']['fb_feMessage']                        = array('Custom text', 'Define a custom button text for the button which lets the user login with his facebook account');

$GLOBALS['TL_LANG']['tl_module']['fb_changeFeMessageConnect']           = array('Custom button text for "Link account with Facebook"');
$GLOBALS['TL_LANG']['tl_module']['fb_feMessageConnectAccount']          = array('Custom text', 'Define a custom button text for linking the user\'s local account with his facebook account');

$GLOBALS['TL_LANG']['tl_module']['fb_feCssAppearance']                  = array('Style front end message', 'Use CSS3 styles to improve the button in the front end.');
$GLOBALS['TL_LANG']['tl_module']['fb_dontUpdateDatabse']                = array('Don\'t update registered members', 'Don\'t update the database if the member still exists (username will never be updated).');
$GLOBALS['TL_LANG']['tl_module']['fb_additionalPermissions']            = array('Load additional fields', 'Choose the fields which should be recieved from Facebook. This requires different permissions. See <a href="https://developers.facebook.com/docs/reference/login/#permissions" target="_blank">https://developers.facebook.com/docs/reference/login/#permissions</a>');
$GLOBALS['TL_LANG']['tl_module']['fb_additionalPermissions']['fields']  = array('email'=>'E-mail adress', 'user_website'=>'Website', 'user_birthday'=>'Birthday', 'user_location'=>'Location');
$GLOBALS['TL_LANG']['tl_module']['fb_customPermissions']                = array('Request for more data fields', 'Comma-separated list of data fields which the facebook app should get access on. This prompts the facebook user for additional permissions. See <a href="https://developers.facebook.com/docs/reference/login/#permissions" target="_blank">https://developers.facebook.com/docs/reference/login/#permissions</a>');

$GLOBALS['TL_LANG']['tl_module']['fb_generateUsernameFrom']             = array('Generate user name from...', 'Choose which data will be used to generate the facebook user\'s local username. Unsafe methods may cause double user names which will produce an error and won\'t let the user create a local account');
$GLOBALS['TL_LANG']['tl_module']['fb_generateUsernameFrom']['fields']   = array('fb_userid'=>'Facebook User ID (safe)', 'email'=>'eMail Address (safe)', 'fb_username'=>'Facebook user name (unsafe)');