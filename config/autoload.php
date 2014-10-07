<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package FacebookConnect
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'alternativeLogin'      => 'system/modules/FacebookConnect/alternativeLogin.php',
	'ModuleFacebookConnect' => 'system/modules/FacebookConnect/ModuleFacebookConnect.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_facebookconnect' => 'system/modules/FacebookConnect/templates',
));
