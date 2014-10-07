<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

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
 * Extends login module, allows Facebook ID and e-mail adress
 * 
 * @package   FacebookConnect
 * @author    Mark Sturm
 * @author    Richard Henkenjohann
 * @copyright Mark Sturm 2013
 */
class alternativeLogin extends Frontend
{
	public function __construct()
	{
		return parent::__construct();
	}

	/**
	 * Get the username
	 */
	public function getUsername($strUsername, $strPassword, $strTable) 
	{
		// Facebook ID
		if(is_numeric($strUsername) && strlen($strUsername) > 9)
		{
			$objUser = $this->Database->prepare('SELECT username FROM ' . $strTable . ' WHERE fb_user_id=?')
									  ->limit(1)
									  ->execute($strUsername);

			if($objUser->numRows)
			{
				$this->Input->setPost('username', $objUser->username);
				return true;
			}
		}
		// E-Mail
		if(strpos($strUsername, '@') !== false)
		{
			$objUser = $this->Database->prepare('SELECT username FROM ' . $strTable . ' WHERE email=?')
									  ->limit(1)
									  ->execute($strUsername);

			if($objUser->numRows)
			{
				$this->Input->setPost('username', $objUser->username);
				return true;
			}
		}
	}
}
