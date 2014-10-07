-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the install *
-- * tool to create and maintain database tables!         *
-- *                                                      *
-- ********************************************************


--
-- Table `tl_page`
--

CREATE TABLE `tl_page` (
  `fb_feed` char(1) NOT NULL default '',
  `fb_appid` varchar(255) NOT NULL default '',
  `fb_secret` varchar(255) NOT NULL default ''
) ENGINE=MyISAM default CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table `tl_member`
--

CREATE TABLE `tl_member` (
  `fb_user_id` varchar(255) NOT NULL default ''
) ENGINE=MyISAM default CHARSET=utf8;


-- ---------------------------------------------------------

--
-- Table  `tl_module`
--
CREATE TABLE `tl_module` (
  `fb_changeFeMessage` char(1) NOT NULL default '',
  `fb_feMessage` varchar(255) NOT NULL default '',

  `fb_changeFeMessageConnect` char(1) NOT NULL default '',
  `fb_feMessageConnectAccount` varchar(255) NOT NULL default '',

  `fb_feCssAppearance` char(1) NOT NULL default '',
  `fb_dontUpdateDatabase` char(1) NOT NULL default '',
  `fb_additionalPermissions` blob NULL,
  `fb_customPermissions` blob NULL,
  `fb_generateUsernameFrom` blob NULL,
) ENGINE=MyISAM default CHARSET=utf8;