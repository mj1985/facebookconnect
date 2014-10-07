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

/*
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_member']['subpalettes']['login'] = str_replace('username,', 'username,fb_user_id,', $GLOBALS['TL_DCA']['tl_member']['subpalettes']['login']);


/*
 * Fields
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['fb_user_id'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_member']['fb_user_id'],
	'exclude'                 => true,
	'search'                  => true,
	'sorting'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'w50', 'unique'=>true, 'rgxp'=>'digit', 'nospace'=>true, 'maxlength'=>64, 'feEditable'=>false, 'feViewable'=>true, 'feGroup'=>'login'),
);

// Customize tl_class for column
$GLOBALS['TL_DCA']['tl_member']['fields']['username']['eval']['tl_class'] = 'w50';