<?php

// $Id$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

if (!isset($title)) $title = 'Tiki Installer';
if (!isset($content)) $content = 'No content specified. Something went wrong.<br/>Please tell your administrator.<br/>If you are the administrator, you may want to check for / file a bug report.';
if (!isset($dberror)) $dberror = false;

// Check that PHP version is at least 5
if (version_compare(PHP_VERSION, '5.0.0', '<')) {
	$title = 'PHP5 is required for Tiki 3.0';
	$content = '<p>Please contact your system administrator ( if you are not the one ;) ).</p>';
	createPage($title, $content);
}

// if tiki installer is locked (probably after previous installation) display notice
if (file_exists('db/lock')) {
	$title = 'Tiki Installer Disabled';
	$content = '
							<p>As a security precaution, the Tiki Installer has been disabled. To re-enable the installer:</p>
							<div style="border: solid 1px #ccc; margin: 1em auto; width: 40%;">
								<ol style="text-align: left">
									<li>Use your file manager application to find the directory where you have unpacked your Tiki and remove the <strong><code>lock</code></strong> file which was created in the <strong><code>db</code></strong> folder.</li>
									<li>Re-run <strong><a href="tiki-install.php" title="Tiki Installer">tiki-install.php</a></strong>.</li>
								</ol>
							</div>';
	createPage($title, $content);
}

$tikiroot = dirname($_SERVER['PHP_SELF']);
$session_params = session_get_cookie_params();
session_set_cookie_params($session_params['lifetime'], $tikiroot);
unset($session_params);
session_start();

require_once 'lib/core/lib/TikiDb/Adodb.php';

/**
 * 
 */
class InstallerDatabaseErrorHandler implements TikiDb_ErrorHandler
{
	function handle(TikiDb $db, $query, $values, $result) {
	}
}

// Were database details defined before? If so, load them
if (file_exists('db/local.php')) {
	include 'db/local.php';
	include_once 'lib/adodb/adodb.inc.php';
	$dbTiki = ADONewConnection($db_tiki);
	$db = new TikiDb_Adodb($dbTiki);
	$db->setErrorHandler(new InstallerDatabaseErrorHandler);
	TikiDb::set($db);

	// check for provided login details and check against the old, saved details that they're correct
	if (isset($_POST['dbuser'], $_POST['dbpass'])) {
		if (($_POST['dbuser'] == $user_tiki) && ($_POST['dbpass'] == $pass_tiki)) {
			$_SESSION['accessible'] = true;
		}
	}
} else {
	// No database info found, so it's a first-install and thus installer is accessible
	$_SESSION['accessible'] = true;
}

if (isset($_SESSION['accessible'])) {
	// allowed to access installer, include it
	$logged = true;
	$admin_acc = 'y';
	include_once 'installer/tiki-installer.php';
} else {
	// Installer knows db details but no login details were received for this script.
	// Thus, display a form.
	$title = 'Tiki Installer Security Precaution';
	$content = '
							<p>&nbsp;</p>
							<p>You are attempting to run the Tiki Installer. For your protection, this installer can be used only by a site administrator.</p>
							<p>To verify that you are a site administrator, enter your <strong><em>database</em></strong> credentials (database username and password) here.</p>
							<p>If you have forgotten your database credentials, find the directory where you have unpacked your Tiki and have a look inside the <strong><code>db</code></strong> folder into the <strong><code>local.php</code></strong> file.</p>
							<form method="post" action="tiki-install.php">
								<p><label for="dbuser">Database username</label>: <input type="text" name="dbuser"/></p>
								<p><label for="dbpass">Database password</label>: <input type="password" name="dbpass"/></p>
								<p><input type="submit" value=" Validate and Continue "/></p>
							</form>
							<p>&nbsp;</p>';
	createPage($title, $content);
}



function createPage($title, $content){
	echo <<<END
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link type="text/css" href="styles/strasa.css" rel="stylesheet" />
		<style type="text/css" media="screen">
html {
	background-color: #fff;
}
#centercolumn {
	padding: 4em 10em;
}
		</style>
		<title>$title</title>
	</head>
	<body class="tiki_wiki" style="text-align: center;">
		<div id="siteheader">
			<div id="sitelogo" style="text-align: center; padding-left: 70px;">
				<img style="border: medium none ;" alt="Site Logo" src="img/tiki/tiki3.png" />
			</div>
		</div>
		<div id="tiki-main">
			<div id="tiki-mid">
				<table id="tiki-midtbl" width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td id="centercolumn" style="text-align:center; vertical-align:top">
							<h1>$title</h1>
							$content
						</td>
					</tr>
				</table>
			</div>
			<div id="tiki-bot" align="center">
				<a title="This is TikiWiki CMS/Groupware" href="http://info.tikiwiki.org" target="_blank"><img src="img/tiki/tikibutton2.png" alt="TikiWiki" border="0" /></a>
			</div>
		</div>
	</body>
</html>
END;
	die;
}
