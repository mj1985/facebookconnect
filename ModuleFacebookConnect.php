<?php if (!defined('TL_ROOT'))
    die('You cannot access this file directly!');

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
 * Handle Facebook registration; update, login, create new members,
 * connect user accounts with their facebook account
 *
 * @package   FacebookConnect
 * @author    Mark Sturm
 * @author    Richard Henkenjohann
 * @author    Michael Fuchs - michael@derfuchs.net
 * @copyright Mark Sturm, Michael Fuchs 2014
 * Special thanks to Andreas Schempp for autoregistration.
 */
require_once(TL_ROOT . '/system/modules/FacebookConnect/assets/src/facebook.php');

class ModuleFacebookConnect extends Module
{

    /**
     * defines which template file should be used
     *
     * (default value: 'mod_facebookconnect')
     *
     * @var string
     * @access protected
     */
    protected $strTemplate = 'mod_facebookconnect';


    /**
     * stores the authentication credentials for the used Facebook app
     *
     * note: Please use getter method getFbAppCredentials() instead of acessing
     * this property directly
     *
     * @var Object (DB Result)
     * @access protected
     */
    protected $objFbAppCredentials = null;

    /**
     * An instance of Facebook's communication library
     *
     * note: Please use getter method getFbApp() instead of acessing
     * this property directly
     *
     * @var Object of Facebook
     * @access protected
     */
    protected $objFbApp = null;

    /**
     * Retrieved Facebook user ID
     *
     * note: Please use getter method getFbUserId() instead of acessing
     * this property directly
     *
     * @var Integer
     * @access protected
     */
    protected $intFbUserId = null;

    /**
     * Processed user data for a Contao frontend user
     *
     * note: Please use method buildContaoUserData() instead of acessing
     * this property directly
     *
     * @var Array
     * @access protected
     */
    protected $arrContaoUserData = null;

    //--------------------------------------------------------------------------

    /**
     * Display a wildcard in the backend
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### USER FACEBOOK-CONNECT ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    //--------------------------------------------------------------------------

    /**
     * Generate the module
     */
    protected function compile()
    {

        // Remember the last visited page for later redirecting
        if ($this->redirectBack)
            $this->Session->set('LAST_PAGE_VISITED', $this->getReferer());

        $this->import('Files');

        $objFbAppCredentials = $this->getFbAppCredentials();

        // Has the module been configured in the website's root page?
        if ($objFbAppCredentials->fb_feed
            && strlen($objFbAppCredentials->fb_appid) > 0
            && strlen($objFbAppCredentials->fb_secret) > 0)
        {

            // Set some template vars
            $this->Template->fb_user_id     = $this->getFbUserId();
            $this->Template->fb_login_url   = $this->getFbLoginUrl();

            /*
                After the user has clicked on the facebook login button, this
                module runs again in the popup.
                $this->getFbUserId() will retrieve a number higher than 0
                and there will be a "code" parameter in the url if facebook
                answered correctly.
             */
            if ($this->getFbUserId() > 0
                && $this->Input->get('code'))
            {

                $arrData = $this->buildContaoUserData($this->getFbApp()->api('/me'));

                // Check if member's eMail or Facebook ID already exists if facebook retrieved an eMail adress
                if (isset($arrData['email']) && !empty($arrData['email']))
                    $objMember = $this->Database->prepare("SELECT id,username,fb_user_id FROM tl_member WHERE fb_user_id=? OR email=?")
                                                    ->execute($this->getFbUserId(), $arrData['email']);
                else
                    $objMember = $this->Database->prepare("SELECT id,username,fb_user_id FROM tl_member WHERE fb_user_id=?")
                                                    ->execute($this->getFbUserId());

                // Case 1: Facebook user does not have a local contao account
                if ($objMember->numRows == 0)
                    $this->addMember($arrData);

                // Case 2: user's eMail address is known, but not linked to a facebook account
                if ($objMember->numRows > 0
                    && empty($objMember->fb_user_id)
                    && $arrData['email'])
                {
                    unset($arrData['activation']);  // Do not update the activation code
                    unset($arrData['username']);    // Do not update the username
                    unset($arrData['dateAdded']);   // Do not update the registration date
                    unset($arrData['email']);       // Do not update member's eMail address
                    unset($arrData['language']);    // Do not update the language setting
                    $this->updateMember($objMember, $arrData);
                    $this->log('User "' . $objMember->username . '" connected his/her account with Facebook ID ' . $this->getFbUserId(), get_class($this) . ' ' . __FUNCTION__ . '()', TL_ACCESS);
                }

                // Case 3: Facebook ID is known and linked to a local contao account --> Sync data
                if ($objMember->numRows > 0
                    && !empty($objMember->fb_user_id)
                    && !($this->fb_dontUpdateDatabase == '1')) {
                    unset($arrData['activation']);  // Do not update the activation code
                    unset($arrData['username']);    // Do not update the username, maybe it has been changed by hand
                    unset($arrData['dateAdded']);   // Do not update the registration date
                    unset($arrData['email']);       // Do not update member's eMail address
                    unset($arrData['language']);    // Do not update the language setting

                    $this->updateMember($objMember, $arrData);
                    $this->log('Facebook user "' . $objMember->username . '" was updated', get_class($this) . ' ' . __FUNCTION__ . '()', TL_ACCESS);
                }

                if ($this->getFbUserId() > 0) {
                    $this->loginMember($this->getFbUserId());
                }

            }
            // If error or user abort close the Popup
            elseif ($this->Input->get('error'))
            {
                $GLOBALS['TL_HEAD'][] = '<script>window.close();</script>';
            }
        }
    }

    //--------------------------------------------------------------------------

    /**
     * Get the authentication credentials for the used Facebook app
     *
     * This method uses LazyLoading
     *
     * @access protected
     * @return Object (DB Result)
     */
    protected function getFbAppCredentials()
    {
        if ($this->objFbAppCredentials == null) {
            global $objPage;
            $this->objFbAppCredentials = $this->Database->prepare('SELECT fb_feed, fb_appid, fb_secret FROM tl_page WHERE id=?')
                                   ->execute($objPage->rootId);
        }
        return $this->objFbAppCredentials;
    }

    //--------------------------------------------------------------------------

    /**
     * Get an ready-to-use instance of Facebook's communication library.
     *
     * This method uses LazyLoading
     *
     * @access protected
     * @return void
     */
    protected function getFbApp()
    {
        if ($this->objFbApp == null) {

            $fbAppCredentials = $this->getFbAppCredentials();

            // Create Facebook application instance
            $this->objFbApp = new Facebook(array
            (
                'appId'     => $fbAppCredentials->fb_appid,
                'secret'    => $fbAppCredentials->fb_secret,
                'cookie'    => true
            ));
        }

        return $this->objFbApp;
    }

    //--------------------------------------------------------------------------

    /**
     * Get the user's ID from the Facebook app.
     *
     * Will return 0 when Facebook user has been not been authenticated.
     *
     * This method uses LazyLoading
     *
     * @access protected
     * @return Numeric
     */
    protected function getFbUserId()
    {
        if ($this->intFbUserId == null)
            $this->intFbUserId = $this->getFbApp()->getUser();

        return $this->intFbUserId;
    }

    //--------------------------------------------------------------------------

    /**
     * Generates an URL which will be called by clicking the FBAuth-Button
     *
     * If the facebook user has to grant additional Permissions, which can be
     * set in the contao Backend this URL will generate a request for them, too.
     *
     * @access protected
     * @return String (URL)
     */
    protected function getFbLoginUrl()
    {
        $arrRequestData = array(
            'display' => 'popup'
        );

        // Get additional requested Permissions for the data request
        $arrRequestedFbData = deserialize($this->fb_additionalPermissions);

        // Are there extended profile and/or e-mail permissions which should be retrieved?
        if ($arrRequestedFbData)
            $arrRequestData['scope'] = implode(',', $arrRequestedFbData); /* email,user_website,user_birthday */

        // Are there any custom profile permissions which should be accessible?
        if (!empty($this->fb_customPermissions))
            $arrRequestData['scope'] .= ',' . $this->fb_customPermissions;


        return $this->getFbApp()->getLoginUrl($arrRequestData);
    }

    //--------------------------------------------------------------------------

    /**
     * Generates a contao URL which will be called after the user successfully
     * authenticated his Facebook connection.
     *
     * Either the last visited URL can be called or a special one which can be
     * set in the contao backend in this module's settings.
     *
     * @access protected
     * @return String (URL)
     */
    protected function getRedirectUrl()
    {

        $strRedirectUrl = '';

        // Redirect to the last page visited
        if ($this->redirectBack && strlen($this->Session->get('LAST_PAGE_VISITED')))
        {
            $strRedirectUrl = $this->Session->get('LAST_PAGE_VISITED');
        }
        // Redirect to the jumpTo page
        else if (strlen($this->jumpTo)){
                $objNextPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
                                    ->limit(1)
                                    ->execute($this->jumpTo);

                if ($objNextPage->numRows)
                    $strRedirectUrl = $this->generateFrontendUrl($objNextPage->fetchAssoc());
            
        }
        // Redirect to same page
        else {
        	$strRedirectUrl = \Environment::get('indexFreeRequest');
        }

        return $strRedirectUrl;
    }

    //--------------------------------------------------------------------------

    /**
     * Builds a data array which models Contao's user data structure in the DB,
     * based on the user data retrieved from facebook and this module's
     * settings.
     *
     * This method uses LazyLoading
     *
     * @access protected
     * @param Array $arrFbUserData
     * @return Array
     */
    protected function buildContaoUserData($arrFbUserData)
    {

        if ($this->arrContaoUserData == null)
        {

            try {
                // Set first user information for database update
                $arrData['tstamp']      = time();
                $arrData['activation']  = md5(uniqid(mt_rand(), true));
                $arrData['dateAdded']   = $arrData['tstamp'];
                $arrData['firstname']   = $arrFbUserData['first_name']; /* John */
                $arrData['lastname']    = $arrFbUserData['last_name']; /* Smith */
                $arrData['gender']      = $arrFbUserData['gender']; /* male,female */
                $arrData['username']    = $this->buildMemberUserName($arrFbUserData);
                $arrData['fb_user_id']  = $this->getFbUserId(); /* 1000012345678912 */
                $arrData['login']       = '1';
                $arrData['groups']      = $this->reg_groups;
                $arrData['language']    = substr($arrFbUserData['locale'], 0, 2); /* en_US */

                $arrRequestedFbData = deserialize($this->fb_additionalPermissions);

                if (in_array('email', $arrRequestedFbData)
                    && $arrFbUserData['email']) // can be NULL. Prevent that.
                {
                    $arrData['email'] = $arrFbUserData['email'];
                }

                if (in_array('user_website', $arrRequestedFbData)
                    && $arrFbUserData['website']) // can be NULL. Prevent that.
                {
                    $arrData['website'] = $arrFbUserData['website'];
                }

                if (in_array('user_birthday', $arrRequestedFbData)
                    && $arrFbUserData['birthday']) // can be NULL. Prevent that.
                {
                    $objDateOfBirth = new Date($arrFbUserData['birthday'], 'm/d/Y'); /* 02/22/1990 */
                    $arrData['dateOfBirth'] = $objDateOfBirth->dayBegin;
                }

                if (in_array('user_location', $arrRequestedFbData)
                    && $arrFbUserData['location']) // can be NULL. Prevent that.
                {
                    // Facebook delivers 3 different location formats
                    $arrLocation = explode(', ', $arrFbUserData['location']['name']);
                    switch (count($arrLocation))
                    {
                        case 3: // Berlin, Berlin, Germany
                            require_once(TL_ROOT . '/system/config/countries.php'); // get the $countries variable
                            $arrData['city'] = $arrLocation[0];
                            $arrData['state'] = $arrLocation[1];
                            $arrData['country'] = str_replace($arrLocation[2], array_search($arrLocation[2], $countries), $arrLocation[2]);
                            break;

                        case 2: // Berlin, Germany
                            require_once(TL_ROOT . '/system/config/countries.php'); // get the $countries variable
                            $arrData['city'] = $arrLocation[0];
                            $arrData['country'] = str_replace($arrLocation[1], array_search($arrLocation[1], $countries), $arrLocation[1]);
                            break;

                        default: // Whatever
                            if (!empty($arrFbUserData['location']['name']))
                                $arrData['city'] = $arrFbUserData['location']['name'];
                            break;
                    }
                }

                $this->arrContaoUserData = $arrData;

            }
            catch (FacebookApiException $e)
            {
                error_log($e);
            }

        }

        return $this->arrContaoUserData;
    }

    //--------------------------------------------------------------------------

    /**
     * Generates a frontend member username, based on facebook's username, email
     * address, or the user's Facebook ID.
     *
     * Fallback: Facebook ID
     *
     * @access protected
     * @param Array $arrFbUserData
     * @return String
     */
    protected function buildMemberUserName($arrFbUserData)
    {
        $strResult = $this->getFbUserId();

        $strGenerateUsernameFrom = deserialize($this->fb_generateUsernameFrom);

        switch ($strGenerateUsernameFrom)
        {
            case 'fb_username':
                $strResult = (isset($arrFbUserData['username']) && is_string($arrFbUserData['username'])) ? $arrFbUserData['username'] : $strResult;
                break;
            case 'email':
                $strResult = (isset($arrFbUserData['email']) && is_string($arrFbUserData['email'])) ? $arrFbUserData['email'] : $strResult;
                break;
            case 'fb_userid':
                $strResult = $this->getFbUserId();
                break;
        }

        return $strResult;
    }

    //--------------------------------------------------------------------------

    /**
     * Add a frontend member to Contao's database, based on $arrMemberData
     *
     * @access protected
     * @param Array $arrMemberData
     * @return void
     */
    protected function addMember($arrMemberData)
    {
		$objNewUser = new \MemberModel();
		$objNewUser->setRow($arrMemberData);
		$objNewUser->save();

        $this->log('User "' . $arrMemberData['username'] . '" with Facebook ID "' . $this->getFbUserId() . '" has been created', get_class($this) . ' ' . __FUNCTION__ . '()', TL_ACCESS);
    }

    //--------------------------------------------------------------------------

    /**
     * creates a home directory for given fontend member
     *
     * @access protected
     * @param object $objMember
     * @return void
     */
    protected function createHomeDir($objMember)
    {
        $objHomeDir = \FilesModel::findByUuid($this->reg_homeDir);

		if ($objHomeDir !== null && $this->getMemberDir($objMember) == null)
		{

            $strUserDir = $this->getMemberFolderName($objMember);

            // Create the user folder
            new \Folder($objHomeDir->path . '/' . $strUserDir);
            $objUserDir = \FilesModel::findByPath($objHomeDir->path . '/' . $strUserDir);

            // Save the folder ID
            $objMember->assignDir = 1;
            $objMember->homeDir = $objUserDir->uuid;
            $objMember->save();

			$this->log('Homedir "' . $objUserDir->path . '" for user with Facebook ID "' . $this->getFbUserId() . '" has been created', get_class($this) . ' ' . __FUNCTION__ . '()', TL_ACCESS);

        }
    }

    //--------------------------------------------------------------------------

    /**
     * returns a contao file object of the member's home dir.
     *
     * @access protected
     * @param object $objMember
     * @return object
     */
    protected function getMemberDir($objMember)
    {
        $objHomeDir = \FilesModel::findByUuid($this->reg_homeDir);
        return \FilesModel::findByPath($objHomeDir->path . '/' . $this->getMemberFolderName($objMember));
    }

    //--------------------------------------------------------------------------

    /**
     * returns the member's home dir name.
     *
     * @access protected
     * @param object $objMember
     * @return string
     */
    protected function getMemberFolderName($objMember)
    {
        return 'user_' . $objMember->id;;
    }

    //--------------------------------------------------------------------------

    /**
     * Updates $objMember's data stored in $arrMemberData.
     *
     * @access protected
     * @param Object (DB Result) $objMember
     * @param Array $arrMemberData
     * @return void
     */
    protected function updateMember($objMember, $arrMemberData)
    {
        $this->Database->prepare("UPDATE tl_member %s WHERE id=?")
                                   ->set($arrMemberData)
                                   ->execute($objMember->id);
    }

    //--------------------------------------------------------------------------

    /**
     * Login the user who is connected to the $intFbUserId
     *
     * @access protected
     * @param Numeric $intFbUserId
     * @return void
     */
    protected function loginMember($intFbUserId)
    {

        // Create new database object to make sure the current member is selected
        $objMember = $this->Database->prepare("SELECT id,username FROM tl_member WHERE fb_user_id=?")
                        ->execute($intFbUserId);

        // create user dir if auto creation is enabled
        if ($this->reg_assignDir)
            $this->createHomeDir(\MemberModel::findBy('id', $objMember->id));

        // Set time variable
        $time = time();

        // Generate the cookie hash
        $strHash = sha1(session_id() . (!$GLOBALS['TL_CONFIG']['disableIpCheck'] ? $this->Environment->ip : '') . 'FE_USER_AUTH');

        // Clean up old sessions
        $this->Database->prepare("DELETE FROM tl_session WHERE tstamp<? OR hash=?")
                       ->execute(($time - $GLOBALS['TL_CONFIG']['sessionTimeout']), $strHash);

        // Save the session in the database
        $this->Database->prepare("INSERT INTO tl_session (pid, tstamp, name, sessionID, ip, hash) VALUES (?, ?, ?, ?, ?, ?)")
                       ->execute($objMember->id, $time, 'FE_USER_AUTH', session_id(), $this->Environment->ip, $strHash);

        // Set the authentication cookie
        $this->setCookie('FE_USER_AUTH', $strHash, ($time + $GLOBALS['TL_CONFIG']['sessionTimeout']), $GLOBALS['TL_CONFIG']['websitePath']);

        // Save the login status
        $this->Session->set('TL_USER_LOGGED_IN', true);

        $this->log('Facebook user "' . $objMember->username . '" was logged in', get_class($this) . ' ' . __FUNCTION__ . '()', TL_ACCESS);

        // HOOK: post login callback
        if (isset($GLOBALS['TL_HOOKS']['postLogin']) && is_array($GLOBALS['TL_HOOKS']['postLogin']))
        {
            foreach ($GLOBALS['TL_HOOKS']['postLogin'] as $callback)
            {
                $this->import($callback[0], 'objLogin', true);
                $this->objLogin->$callback[1]($objMember);
            }
        }

        // Close Popup and redirect
        $GLOBALS['TL_HEAD'][] = '<script>window.close(); window.opener.location.href = "' . $this->getRedirectUrl() . '"</script>';


    }

    //--------------------------------------------------------------------------

}
