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

/*
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD']['user'], 1, array
(
	'FacebookConnect' => 'ModuleFacebookConnect'
));

/*
 * Hooks
 */
$GLOBALS['TL_HOOKS']['importUser'][] = array('alternativeLogin', 'getUsername');
