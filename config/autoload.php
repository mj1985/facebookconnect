<?php

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
