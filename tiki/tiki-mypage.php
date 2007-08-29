<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mypage.php,v 1.20 2007-08-29 07:55:50 tombombadilom Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/mypage/mypagelib.php');
require_once ('lib/ajax/ajaxlib.php');


if ($feature_ajax != "y" || $feature_mootools != "y") {
	$smarty->assign('msg', tra("ajax and mootools features mandatory for that feature"));
	$smarty->assign('errortype', '402');
	$smarty->display("error.tpl");
	die;

}
if (strlen($user) <= 0) {
	$id_users=0;
} else {
	$id_users=$userlib->get_user_id($user);
}

function mypage_ajax_init() {
	global $ajaxlib;

	//$ajaxlib->debugOn();
	$ajaxlib->setRequestURI("tiki-mypage_ajax.php");
	$ajaxlib->registerFunction("mypage_win_setrect");
	$ajaxlib->registerFunction("mypage_win_destroy");
	$ajaxlib->registerFunction("mypage_win_create");
    $ajaxlib->registerFunction("mypage_win_prepareConfigure");
    $ajaxlib->registerFunction("mypage_win_configure");
	$ajaxlib->registerFunction("mypage_update");
	$ajaxlib->processRequests();
}

function mypage_init() {
	global $smarty, $headerlib, $id_users;

	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.css");
	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.aero.css");
	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.alphacube.css");
	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.aqua.css");
	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.nada.css");

	$headerlib->add_jsfile("lib/mootools/extensions/mooRainbow/mooRainbow.js");
	$headerlib->add_cssfile("lib/mootools/extensions/mooRainbow/mooRainbow.css");

	$smarty->assign("mootools",'y');
	$smarty->assign("mootab",'y');
	$smarty->assign("mootools_windoo",'y');

	$id_mypage=isset($_REQUEST['id_mypage']) ? (int)$_REQUEST['id_mypage'] : 0;
	$mypage=NULL;
	if (!$id_mypage) {
		$pagename=isset($_REQUEST['mypage']) ? $_REQUEST['mypage'] : '';
		$smarty->assign('pagename',$pagename);
		if (!empty($pagename)) {
			$mypage=MyPage::getMyPage_byName($pagename, $id_users);			
		}
	} else {
		$mypage=MyPage::getMyPage_byId($id_mypage, $id_users);
	}

	if (!$mypage || is_string($mypage)) {
		die("mypage not found : ".$mypage);
	}

	$mypage->viewed(); // increment viewed count

	$id_mypage=$mypage->id;

	// TODO: verify that we have permission to edit it
	if (isset($_REQUEST['edit']) && $_REQUEST['edit'] == 1) $editit=true;
	else $editit=false;
	$smarty->assign('editit', $editit);

	$smarty->assign('mypagejswindows', $mypage->getJSCode($editit));
	$smarty->assign('id_mypage', $id_mypage);

	$width=$mypage->getParam('width');
	$height=$mypage->getParam('height');
	$width=($width == 0 ? '100%' : $width.'px');
	$height=($height == 0 ? '100%' : $height.'px');

	$smarty->assign('mypage_width', $width);
	$smarty->assign('mypage_height', $height);

	$bgcolor=$mypage->getParam('bgcolor');
	if ($bgcolor === NULL) $bgcolor='#eeeeee';
	$smarty->assign('mypage_bgcolor', $bgcolor);

	$smarty->assign('components', $mypage->getAvailableComponents());

	// deactivate left and right columns 
	$smarty->assign('feature_right_column', 'n');
	$smarty->assign('feature_left_column', 'n');


	mypage_ajax_init();

	$smarty->assign("mid", "tiki-mypage.tpl");
	$smarty->display("tiki.tpl");

}


mypage_init();

/* For the emacs weenies in the crowd.
Local Variables:
   tab-width: 4
   c-basic-offset: 4
End:
*/

?>
