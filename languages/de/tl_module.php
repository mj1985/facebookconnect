<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   FacebookConnect
 * @author    Mark Sturm
 * @author    Richard Henkenjohann
 * @author    Michael Fuchs - michael@derfuchs.net
 * @copyright Mark Sturm, Michael Fuchs 2014
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_module']['fb_settings_legend']	= 'Moduleinstellungen';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['fb_changeFeMessage']                  = array('Eigener Text für Button: "Mit Facebook anmelden"');
$GLOBALS['TL_LANG']['tl_module']['fb_feMessage']                        = array('Button-Text', 'Bestimmen Sie einen eigenen Text für den Button, der dem Nutzer ermöglicht, sich mit seinem Facebook-Profil anzumelden.');

$GLOBALS['TL_LANG']['tl_module']['fb_changeFeMessageConnect']           = array('Eigener Text für Button: "Account mit Facebook verbinden"');
$GLOBALS['TL_LANG']['tl_module']['fb_feMessageConnectAccount']          = array('Button-Text', 'Bestimmen Sie einen eigenen Text für den Button, der dem Nutzer ermöglicht, seinen bestehenden lokalen Account mit seinem Facebook-Profil zu verknüpfen');

$GLOBALS['TL_LANG']['tl_module']['fb_feCssAppearance']                  = array('Frontend-Ausgabe stylen', 'Wählen Sie aus, ob Sie die Ausgabe im Frontend mit CSS3 stylen wollen.');
$GLOBALS['TL_LANG']['tl_module']['fb_dontUpdateDatabse']                = array('Bereits registrierten Benutzer nicht updaten', 'Wählen Sie dies, wenn Sie nicht wollen, dass der bereits registrierte Benuzter aktualisiert wird (Benutzername wird generell nicht aktualisiert).');
$GLOBALS['TL_LANG']['tl_module']['fb_additionalPermissions']            = array('Diese Nutzerdaten von Facebook laden', 'Wählen Sie aus, welche Felder zusätzlich von Facebook geladen werden sollen. Dies erfordert eine zusätzliche Einwilligung des Nutzers. Siehe <a href="https://developers.facebook.com/docs/reference/login/#permissions" target="_blank">https://developers.facebook.com/docs/reference/login/#permissions</a>');
$GLOBALS['TL_LANG']['tl_module']['fb_additionalPermissions']['fields']  = array('email'=>'E-Mail-Adresse', 'user_website'=>'Webseite', 'user_birthday'=>'Geburtsdatum', 'user_location'=>'Wohnort und -land');
$GLOBALS['TL_LANG']['tl_module']['fb_customPermissions']                = array('Zugriffserlaubnis für zusätzliche Facebook-Nutzerdaten', 'Eine Komma-getrennte Liste von speziellen Datenfeldern, die bei Facebook abgefragt werden dürfen. Dies erfordert eine zusätzliche Einwilligung des Nutzers. Siehe <a href="https://developers.facebook.com/docs/reference/login/#permissions" target="_blank">https://developers.facebook.com/docs/reference/login/#permissions</a>');


$GLOBALS['TL_LANG']['tl_module']['fb_generateUsernameFrom']             = array('Username-Generierung', 'Definiert, welche Daten zur automatischen Generierung des Nutzer-Namens heran gezogen werden sollen. Unsichere Methoden können aufgrund von Doppelungen von Nutzernamen dazu führen, dass sich ein Nutzer nicht über Facebook anmelden kann.');
$GLOBALS['TL_LANG']['tl_module']['fb_generateUsernameFrom']['fields']   = array('fb_userid'=>'Facebook Nutzer-ID (sicher)', 'email'=>'E-Mail-Adresse (sicher)', 'fb_username'=>'Facebook-Username (unsicher)');