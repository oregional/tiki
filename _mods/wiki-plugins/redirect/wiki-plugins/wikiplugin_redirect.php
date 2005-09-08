<?php

// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/redirect/wiki-plugins/wikiplugin_redirect.php,v 1.5 2005-09-08 02:43:15 damosoft Exp $

// Wiki plugin to redirect to another page.
// damian aka damosoft 30 March 2004

function wikiplugin_redirect_help() {
        return tra("Redirects you to another wiki page").":<br />~np~{REDIRECT(page=pagename)/}~/np~";
}

function wikiplugin_redirect($data, $params) {

	extract ($params,EXTR_SKIP);
	$areturn = '';

	if (!isset($page)) {

		$areturn = "REDIRECT plugin: No page specified!";
	
	} else {
		if ((isset($_REQUEST['redirectpage']))) {
			$areturn = "REDIRECT plugin: redirect loop detected!";
		}else{
			header("Location: tiki-index.php?page=$page&redirectpage=".$_REQUEST['page']);
			exit;
		}
	}

	return $areturn;
}

?>
