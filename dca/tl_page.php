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
 * Palettes / Subpalettes
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][]	= 'fb_feed';
$GLOBALS['TL_DCA']['tl_page']['palettes']['root']			= str_replace('{sitemap_legend:hide}', '{fb_connect_legend},fb_feed;{sitemap_legend:hide}', $GLOBALS['TL_DCA']['tl_page']['palettes']['root']);
$GLOBALS['TL_DCA']['tl_page']['palettes']['fb_feed']		= str_replace('{fb_connect_legend},fb_feed', '{fb_connect_legend},fb_feed,fb_appid,fb_secret', $GLOBALS['TL_DCA']['tl_page']['palettes']['root']);

/*
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['fb_feed']		= array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_page']['fb_feed'],
	'exclude'				  => true,
	'inputType'				  => 'checkbox',
	'eval'					  => array('submitOnChange'=>true)
);

$GLOBALS['TL_DCA']['tl_page']['fields']['fb_appid']		= array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_page']['fb_appid'],
	'exclude'				  => true,
	'inputType'				  => 'text',
	'eval'					  => array('mandatory'=>true, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_page']['fields']['fb_secret']	= array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_page']['fb_secret'],
	'exclude'				  => true,
	'inputType'			 	  => 'text',
	'eval'					  => array('mandatory'=>true, 'tl_class'=>'w50')	
);
