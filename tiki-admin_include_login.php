<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_login.php,v 1.30 2004-10-15 15:54:42 damosoft Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["loginprefs"])) {
	check_ticket('admin-inc-login');
    if (isset($_REQUEST["change_theme"]) && $_REQUEST["change_theme"] == "on") {
	$tikilib->set_preference("change_theme", 'y');
    } else {
	$tikilib->set_preference("change_theme", 'n');
    }

    if (isset($_REQUEST["change_language"]) && $_REQUEST["change_language"] == "on") {
	$tikilib->set_preference("change_language", 'y');
    } else {
	$tikilib->set_preference("change_language", 'n');
    }

    if (isset($_REQUEST["change_language"]) && isset($_REQUEST["available_languages"])) {
	$tikilib->set_preference("available_languages", serialize($_REQUEST["available_languages"]));
    } else {
	$tikilib->set_preference("available_languages", serialize(array()));
    }

    if (isset($_REQUEST["available_styles"])) {
	$tikilib->set_preference("available_styles", serialize($_REQUEST["available_styles"]));
    } else {
	$tikilib->set_preference("available_styles", serialize(array()));
    }

    if (isset($_REQUEST["eponymousGroups"]) &&
	    $_REQUEST["eponymousGroups"] == "on")
    {
	$tikilib->set_preference("eponymousGroups", 'y');

	$smarty->assign('eponymousGroups', 'y');
    } else {
	$tikilib->set_preference("eponymousGroups", 'n');

	$smarty->assign('eponymousGroups', 'n');
    }
		
	if (isset($_REQUEST["userTracker"]) && $_REQUEST["userTracker"] == "on") {
		$tikilib->set_preference("userTracker", 'y');
	} else {
		$tikilib->set_preference("userTracker", 'n');
	}

	if (isset($_REQUEST["groupTracker"]) && $_REQUEST["groupTracker"] == "on") {
		$tikilib->set_preference("groupTracker", 'y');
	} else {
		$tikilib->set_preference("groupTracker", 'n');
	}

    if (isset($_REQUEST["allowRegister"]) && $_REQUEST["allowRegister"] == "on") {
	$tikilib->set_preference("allowRegister", 'y');

	$smarty->assign('allowRegister', 'y');
    } else {
	$tikilib->set_preference("allowRegister", 'n');

	$smarty->assign('allowRegister', 'n');
    }

	if (isset($_REQUEST["validateRegistration"]) && $_REQUEST["validateRegistration"] == "on") {
		$tikilib->set_preference("validateRegistration", 'y');
		$smarty->assign('validateRegistration', 'y');
	} else {
		$tikilib->set_preference("validateRegistration", 'n');
		$smarty->assign('validateRegistration', 'n');
	}

    if (isset($_REQUEST["webserverauth"]) && $_REQUEST["webserverauth"] == "on") {
	$tikilib->set_preference("webserverauth", 'y');

	$smarty->assign('webserverauth', 'y');
    } else {
	$tikilib->set_preference("webserverauth", 'n');

	$smarty->assign('webserverauth', 'n');
    }

    if (isset($_REQUEST["useRegisterPasscode"]) && $_REQUEST["useRegisterPasscode"] == "on") {
	$tikilib->set_preference("useRegisterPasscode", 'y');

	$smarty->assign('useRegisterPasscode', 'y');
    } else {
	$tikilib->set_preference("useRegisterPasscode", 'n');

	$smarty->assign('useRegisterPasscode', 'n');
    }

    $tikilib->set_preference("registerPasscode", $_REQUEST["registerPasscode"]);
    $smarty->assign('registerPasscode', $_REQUEST["registerPasscode"]);

    $tikilib->set_preference("min_pass_length", $_REQUEST["min_pass_length"]);
    $smarty->assign('min_pass_length', $_REQUEST["min_pass_length"]);

    $tikilib->set_preference("pass_due", $_REQUEST["pass_due"]);
    $smarty->assign('pass_due', $_REQUEST["pass_due"]);

    if (isset($_REQUEST["validateUsers"]) && $_REQUEST["validateUsers"] == "on") {
	$tikilib->set_preference("validateUsers", 'y');

	$smarty->assign('validateUsers', 'y');
    } else {
	$tikilib->set_preference("validateUsers", 'n');

	$smarty->assign('validateUsers', 'n');
    }

    if (isset($_REQUEST["validateEmail"]) && $_REQUEST["validateEmail"] == "on") {
        $tikilib->set_preference("validateEmail", 'y');

        $smarty->assign('validateEmail', 'y');
    } else {
        $tikilib->set_preference("validateEmail", 'n');

        $smarty->assign('validateEmail', 'n');
    }


    if (isset($_REQUEST["rnd_num_reg"]) && $_REQUEST["rnd_num_reg"] == "on") {
	$tikilib->set_preference("rnd_num_reg", 'y');

	$smarty->assign('rnd_num_reg', 'y');
    } else {
	$tikilib->set_preference("rnd_num_reg", 'n');

	$smarty->assign('rnd_num_reg', 'n');
    }

    if (isset($_REQUEST["pass_chr_num"]) && $_REQUEST["pass_chr_num"] == "on") {
	$tikilib->set_preference("pass_chr_num", 'y');

	$smarty->assign('pass_chr_num', 'y');
    } else {
	$tikilib->set_preference("pass_chr_num", 'n');

	$smarty->assign('pass_chr_num', 'n');
    }

    if (isset($_REQUEST["feature_challenge"]) && $_REQUEST["feature_challenge"] == "on") {
	$tikilib->set_preference("feature_challenge", 'y');

	$smarty->assign('feature_challenge', 'y');
    } else {
	$tikilib->set_preference("feature_challenge", 'n');

	$smarty->assign('feature_challenge', 'n');
    }

    if (isset($_REQUEST["feature_clear_passwords"]) && $_REQUEST["feature_clear_passwords"] == "on") {
	$tikilib->set_preference("feature_clear_passwords", 'y');

	$smarty->assign('feature_clear_passwords', 'y');
    } else {
	$tikilib->set_preference("feature_clear_passwords", 'n');

	$smarty->assign('feature_clear_passwords', 'n');
    }

    if (isset($_REQUEST["forgotPass"]) && $_REQUEST["forgotPass"] == "on") {
	$tikilib->set_preference("forgotPass", 'y');

	$smarty->assign('forgotPass', 'y');
    } else {
	$tikilib->set_preference("forgotPass", 'n');

	$smarty->assign('forgotPass', 'n');
    }

    /* # not implemented
       $b = isset($_REQUEST['http_basic_auth']) && $_REQUEST['http_basic_auth'] == 'on';
       $tikilib->set_preference('http_basic_auth', $b); 
       $smarty->assign('http_basic_auth', $b);
     */

    $b = (isset($_REQUEST['https_login']) && $_REQUEST['https_login'] == 'on') ? 'y' : 'n';
    $tikilib->set_preference('https_login', $b);
    $tikilib->set_preference('useUrlIndex', 'n');
    $smarty->assign('https_login', $b);
    $smarty->assign('useUrlIndex', 'n');

    $b = (isset($_REQUEST['https_login_required']) && $_REQUEST['https_login_required'] == 'on') ? 'y' : 'n';
    $tikilib->set_preference('https_login_required', $b);
    $tikilib->set_preference('useUrlIndex', 'n');
    $smarty->assign('https_login_required', $b);
    $smarty->assign('useUrlIndex', 'n');

    $v = isset($_REQUEST['http_domain']) ? $_REQUEST['http_domain'] : '';
    $tikilib->set_preference('http_domain', $v);
    $smarty->assign('http_domain', $v);

    $v = isset($_REQUEST['http_port']) ? $_REQUEST['http_port'] : 80;
    $tikilib->set_preference('http_port', $v);
    $smarty->assign('http_port', $v);

    $v = isset($_REQUEST['http_prefix']) ? $_REQUEST['http_prefix'] : '/';
    $tikilib->set_preference('http_prefix', $v);
    $smarty->assign('http_prefix', $v);

    $v = isset($_REQUEST['https_domain']) ? $_REQUEST['https_domain'] : '';
    $tikilib->set_preference('https_domain', $v);
    $smarty->assign('https_domain', $v);

    $v = isset($_REQUEST['https_port']) ? $_REQUEST['https_port'] : 443;
    $tikilib->set_preference('https_port', $v);
    $smarty->assign('https_port', $v);

    $v = isset($_REQUEST['https_prefix']) ? $_REQUEST['https_prefix'] : '/';
    $tikilib->set_preference('https_prefix', $v);
    $smarty->assign('https_prefix', $v);
    $tikilib->set_preference('rememberme', $_REQUEST['rememberme']);
    $tikilib->set_preference('remembertime', $_REQUEST['remembertime']);
    $smarty->assign('rememberme', $_REQUEST['rememberme']);
    $smarty->assign('remembertime', $_REQUEST['remembertime']);
		
		$v = isset($_REQUEST['cookie_name']) ? $_REQUEST['cookie_name'] : $_SERVER['SERVER_NAME'];
    $tikilib->set_preference('cookie_name', $v);
    $smarty->assign('cookie_name', $v);

		$v = isset($_REQUEST['cookie_domain']) ? $_REQUEST['cookie_domain'] : $_SERVER['SERVER_NAME'];
    $tikilib->set_preference('cookie_domain', $v);
    $smarty->assign('cookie_domain', $v);

		$v = isset($_REQUEST['cookie_path']) ? $_REQUEST['cookie_path'] : '/';
    $tikilib->set_preference('cookie_path', $v);
    $smarty->assign('cookie_path', $v);

    if (isset($_REQUEST["auth_method"])) {
	$tikilib->set_preference('auth_method', $_REQUEST['auth_method']);

	$smarty->assign('auth_method', $_REQUEST['auth_method']);
    }

	$b = (isset($_REQUEST['feature_ticketlib']) && $_REQUEST['feature_ticketlib'] == 'on') ? 'y' : 'n';
	$tikilib->set_preference('feature_ticketlib', $b);
	$smarty->assign('feature_ticketlib', $b);

	$b = (isset($_REQUEST['feature_ticketlib2']) && $_REQUEST['feature_ticketlib2'] == 'on') ? 'y' : 'n';
	$tikilib->set_preference('feature_ticketlib2', $b);
	$smarty->assign('feature_ticketlib2', $b);

	$v = isset($_REQUEST['highlight_group']) ? $_REQUEST['highlight_group'] : '';
	$tikilib->set_preference('highlight_group', $v);
	$smarty->assign('highlight_group', $v);
}

if (isset($_REQUEST["auth_pear"])) {
	check_ticket('admin-inc-login');
    if (isset($_REQUEST["auth_create_user_tiki"]) && $_REQUEST["auth_create_user_tiki"] == "on") {
	$tikilib->set_preference("auth_create_user_tiki", 'y');

	$smarty->assign("auth_create_user_tiki", 'y');
    } else {
	$tikilib->set_preference("auth_create_user_tiki", 'n');

	$smarty->assign("auth_create_user_tiki", 'n');
    }

    if (isset($_REQUEST["auth_create_user_auth"]) && $_REQUEST["auth_create_user_auth"] == "on") {
	$tikilib->set_preference("auth_create_user_auth", 'y');

	$smarty->assign("auth_create_user_auth", 'y');
    } else {
	$tikilib->set_preference("auth_create_user_auth", 'n');

	$smarty->assign("auth_create_user_auth", 'n');
    }

    if (isset($_REQUEST["auth_skip_admin"]) && $_REQUEST["auth_skip_admin"] == "on") {
	$tikilib->set_preference("auth_skip_admin", 'y');

	$smarty->assign("auth_skip_admin", 'y');
    } else {
	$tikilib->set_preference("auth_skip_admin", 'n');

	$smarty->assign("auth_skip_admin", 'n');
    }

    if (isset($_REQUEST["auth_ldap_host"])) {
	$tikilib->set_preference("auth_ldap_host", $_REQUEST["auth_ldap_host"]);

	$smarty->assign('auth_ldap_host', $_REQUEST["auth_ldap_host"]);
    }

    if (isset($_REQUEST["auth_ldap_port"])) {
	$tikilib->set_preference("auth_ldap_port", $_REQUEST["auth_ldap_port"]);

	$smarty->assign('auth_ldap_port', $_REQUEST["auth_ldap_port"]);
    }

    if (isset($_REQUEST["auth_ldap_scope"])) {
	$tikilib->set_preference("auth_ldap_scope", $_REQUEST["auth_ldap_scope"]);

	$smarty->assign('auth_ldap_scope', $_REQUEST["auth_ldap_scope"]);
    }

    if (isset($_REQUEST["auth_ldap_basedn"])) {
	$tikilib->set_preference("auth_ldap_basedn", $_REQUEST["auth_ldap_basedn"]);

	$smarty->assign('auth_ldap_basedn', $_REQUEST["auth_ldap_basedn"]);
    }

    if (isset($_REQUEST["auth_ldap_userdn"])) {
	$tikilib->set_preference("auth_ldap_userdn", $_REQUEST["auth_ldap_userdn"]);

	$smarty->assign('auth_ldap_userdn', $_REQUEST["auth_ldap_userdn"]);
    }

    if (isset($_REQUEST["auth_ldap_userattr"])) {
	$tikilib->set_preference("auth_ldap_userattr", $_REQUEST["auth_ldap_userattr"]);

	$smarty->assign('auth_ldap_userattr', $_REQUEST["auth_ldap_userattr"]);
    }

    if (isset($_REQUEST["auth_ldap_useroc"])) {
	$tikilib->set_preference("auth_ldap_useroc", $_REQUEST["auth_ldap_useroc"]);

	$smarty->assign('auth_ldap_useroc', $_REQUEST["auth_ldap_useroc"]);
    }

    if (isset($_REQUEST["auth_ldap_groupdn"])) {
	$tikilib->set_preference("auth_ldap_groupdn", $_REQUEST["auth_ldap_groupdn"]);

	$smarty->assign('auth_ldap_groupdn', $_REQUEST["auth_ldap_groupdn"]);
    }

    if (isset($_REQUEST["auth_ldap_groupattr"])) {
	$tikilib->set_preference("auth_ldap_groupattr", $_REQUEST["auth_ldap_groupattr"]);

	$smarty->assign('auth_ldap_groupattr', $_REQUEST["auth_ldap_groupattr"]);
    }

    if (isset($_REQUEST["auth_ldap_groupoc"])) {
	$tikilib->set_preference("auth_ldap_groupoc", $_REQUEST["auth_ldap_groupoc"]);

	$smarty->assign('auth_ldap_groupoc', $_REQUEST["auth_ldap_groupoc"]);
    }

    if (isset($_REQUEST["auth_ldap_memberattr"])) {
	$tikilib->set_preference("auth_ldap_memberattr", $_REQUEST["auth_ldap_memberattr"]);

	$smarty->assign('auth_ldap_ldap_memberattr', $_REQUEST["auth_ldap_memberattr"]);
    }

    if (isset($_REQUEST["auth_ldap_memberisdn"]) && $_REQUEST["auth_ldap_memberisdn"] == "on") {
	$tikilib->set_preference("auth_ldap_memberisdn", 'y');

	$smarty->assign("auth_ldap_memberisdn", 'y');
    } else {
	$tikilib->set_preference("auth_ldap_memberisdn", 'n');

	$smarty->assign("auth_ldap_memberisdn", 'n');
    }

    if (isset($_REQUEST["auth_ldap_adminuser"])) {
	$tikilib->set_preference("auth_ldap_adminuser", $_REQUEST["auth_ldap_adminuser"]);

	$smarty->assign('auth_ldap_adminuser', $_REQUEST["auth_ldap_adminuser"]);
    }

    if (isset($_REQUEST["auth_ldap_adminpass"])) {
	$tikilib->set_preference("auth_ldap_adminpass", $_REQUEST["auth_ldap_adminpass"]);

	$smarty->assign('auth_ldap_adminpass', $_REQUEST["auth_ldap_adminpass"]);
    }
}

if (isset($_REQUEST["auth_pam"])) {
        check_ticket('admin-inc-login');
    if (isset($_REQUEST["pam_create_user_tiki"]) && $_REQUEST["pam_create_user_tiki"] ==  "on") {
        $tikilib->set_preference("pam_create_user_tiki", 'y');

        $smarty->assign("pam_create_user_tiki", 'y');
    } else {
        $tikilib->set_preference("pam_create_user_tiki", 'n');

        $smarty->assign("pam_create_user_tiki", 'n');
    }
    if (isset($_REQUEST["pam_skip_admin"]) && $_REQUEST["pam_skip_admin"] == "on") {
        $tikilib->set_preference("pam_skip_admin", 'y');

        $smarty->assign("pam_skip_admin", 'y');
    } else {
        $tikilib->set_preference("pam_skip_admin", 'n');

        $smarty->assign("pam_skip_admin", 'n');
    }
    if (isset($_REQUEST["pam_service"])) {
        $tikilib->set_preference("pam_service", $_REQUEST["pam_service"]);

        $smarty->assign('pam_service', $_REQUEST["pam_service"]);
    }
}

if (isset($_REQUEST['auth_cas'])) {
        check_ticket('admin-inc-login');
    if (isset($_REQUEST['cas_create_user_tiki']) && $_REQUEST['cas_create_user_tiki'] ==  'on') {
        $tikilib->set_preference('cas_create_user_tiki', 'y');

        $smarty->assign('cas_create_user_tiki', 'y');
    } else {
        $tikilib->set_preference('cas_create_user_tiki', 'n');

        $smarty->assign('cas_create_user_tiki', 'n');
    }
    if (isset($_REQUEST['cas_skip_admin']) && $_REQUEST['cas_skip_admin'] == 'on') {
        $tikilib->set_preference('cas_skip_admin', 'y');

        $smarty->assign('cas_skip_admin', 'y');
    } else {
        $tikilib->set_preference('cas_skip_admin', 'n');

        $smarty->assign('cas_skip_admin', 'n');
    }
	if (isset($_REQUEST['cas_version'])) {
		$tikilib->set_preference('cas_version', $_REQUEST['cas_version']);

		$smarty->assign('cas_version', $_REQUEST['cas_version']);
	}
	if (isset($_REQUEST['cas_hostname'])) {
		$tikilib->set_preference('cas_hostname', $_REQUEST['cas_hostname']);

		$smarty->assign('cas_hostname', $_REQUEST['cas_hostname']);
	}
	if (isset($_REQUEST['cas_port'])) {
		$tikilib->set_preference('cas_port', $_REQUEST['cas_port']);

		$smarty->assign('cas_port', $_REQUEST['cas_port']);
	}
	if (isset($_REQUEST['cas_path'])) {
		$tikilib->set_preference('cas_path', $_REQUEST['cas_path']);

		$smarty->assign('cas_path', $_REQUEST['cas_path']);
	}
}

// list of user groups is needed by auth_ext_xml and the rendering of Admin/Login
$groups = $userlib->get_groups();
$group_arraylen = count($groups['data']);
$groups_array_set = false;

if (isset($_REQUEST['auth_ext_xml'])) {
	
    check_ticket('admin-inc-login');
    if (isset($_REQUEST['auth_ext_xml_enabled']) && $_REQUEST['auth_ext_xml_enabled'] ==  'on') {
        $tikilib->set_preference('auth_ext_xml_enabled', 'y');
        $smarty->assign('auth_ext_xml_enabled', 'y');
    } else {
        $tikilib->set_preference('auth_ext_xml_enabled', 'n');
        $smarty->assign('auth_ext_xml_enabled', 'n');
    }
    if (isset($_REQUEST['auth_ext_xml_delete_user_tiki']) && $_REQUEST['auth_ext_xml_delete_user_tiki'] ==  'on') {
        $tikilib->set_preference('auth_ext_xml_delete_user_tiki', 'y');
        $smarty->assign('auth_ext_xml_delete_user_tiki', 'y');
    } else {
        $tikilib->set_preference('auth_ext_xml_delete_user_tiki', 'n');
        $smarty->assign('auth_ext_xml_delete_user_tiki', 'n');
    }
    if (isset($_REQUEST['auth_ext_xml_manage_group']) && $_REQUEST['auth_ext_xml_manage_group'] ==  'on') {
        $tikilib->set_preference('auth_ext_xml_manage_group', 'y');
        $smarty->assign('auth_ext_xml_manage_group', 'y');
    } else {
        $tikilib->set_preference('auth_ext_xml_manage_group', 'n');
        $smarty->assign('auth_ext_xml_manage_group', 'n');
    }
    if (isset($_REQUEST['auth_ext_xml_skip_admin']) && $_REQUEST['auth_ext_xml_skip_admin'] == 'on') {
        $tikilib->set_preference('auth_ext_xml_skip_admin', 'y');
        $smarty->assign('auth_ext_xml_skip_admin', 'y');
    } else {
        $tikilib->set_preference('auth_ext_xml_skip_admin', 'n');
        $smarty->assign('auth_ext_xml_skip_admin', 'n');
    }
    if (isset($_REQUEST['auth_ext_xml_cas_proxy']) && $_REQUEST['auth_ext_xml_cas_proxy'] == 'on') {
        $tikilib->set_preference('auth_ext_xml_cas_proxy', 'y');
        $smarty->assign('auth_ext_xml_cas_proxy', 'y');
    } else {
        $tikilib->set_preference('auth_ext_xml_cas_proxy', 'n');
        $smarty->assign('auth_ext_xml_cas_proxy', 'n');
    }
	if (isset($_REQUEST['auth_ext_xml_url'])) {
		$tikilib->set_preference('auth_ext_xml_url', $_REQUEST['auth_ext_xml_url']);
		$smarty->assign('auth_ext_xml_url', $_REQUEST['auth_ext_xml_url']);
	}
	if (isset($_REQUEST['auth_ext_xml_login_element'])) {
		$tikilib->set_preference('auth_ext_xml_login_element', $_REQUEST['auth_ext_xml_login_element']);
		$smarty->assign('auth_ext_xml_login_element', $_REQUEST['auth_ext_xml_login_element']);
	}
	if (isset($_REQUEST['auth_ext_xml_login_element_value'])) {
		$tikilib->set_preference('auth_ext_xml_login_element_value', $_REQUEST['auth_ext_xml_login_element_value']);
		$smarty->assign('auth_ext_xml_login_element_value', $_REQUEST['auth_ext_xml_login_element_value']);
	}
	if (isset($_REQUEST['auth_ext_xml_login_attribute'])) {
		$tikilib->set_preference('auth_ext_xml_login_attribute', $_REQUEST['auth_ext_xml_login_attribute']);
		$smarty->assign('auth_ext_xml_login_attribute', $_REQUEST['auth_ext_xml_login_attribute']);
	}
	if (isset($_REQUEST['auth_ext_xml_login_attribute_value'])) {
		$tikilib->set_preference('auth_ext_xml_login_attribute_value', $_REQUEST['auth_ext_xml_login_attribute_value']);
		$smarty->assign('auth_ext_xml_login_attribute_value', $_REQUEST['auth_ext_xml_login_attribute_value']);
	}
	for ($i=0; $i<$group_arraylen; $i++) {
		$groupname = $groups['data'][$i]['groupName'];
		$auth_ext_xml_manage_group = 'auth_ext_xml_manage_'. $groupname;
		$auth_ext_xml_group_element = 'auth_ext_xml_element_' . $groupname;
		$auth_ext_xml_group_element_value = 'auth_ext_xml_element_val_' . $groupname;
		$auth_ext_xml_group_attribute = 'auth_ext_xml_attr_' . $groupname;
		$auth_ext_xml_group_attribute_value = 'auth_ext_xml_attr_val_' . $groupname;
		global $$auth_ext_xml_manage_group;
		global $$auth_ext_xml_group_element;
		global $$auth_ext_xml_group_element_value;
		global $$auth_ext_xml_group_attribute;
		global $$auth_ext_xml_group_attribute_value;
	    if (isset($_REQUEST["$auth_ext_xml_manage_group"]) && $_REQUEST["$auth_ext_xml_manage_group"] ==  'on') {
	        $tikilib->set_preference("$auth_ext_xml_manage_group", 'y');
	        $$auth_ext_xml_manage_group = 'y';
	    } else {
	        $tikilib->set_preference("$auth_ext_xml_manage_group", 'n');
	        $$auth_ext_xml_manage_group = 'n';
	    }
		if (isset($_REQUEST["$auth_ext_xml_group_element"])) {
			$tikilib->set_preference("$auth_ext_xml_group_element", $_REQUEST["$auth_ext_xml_group_element"]);
			$$auth_ext_xml_group_element = $_REQUEST["$auth_ext_xml_group_element"];
		}
		if (isset($_REQUEST["$auth_ext_xml_group_element_value"])) {
			$tikilib->set_preference("$auth_ext_xml_group_element_value", $_REQUEST["$auth_ext_xml_group_element_value"]);
			$$auth_ext_xml_group_element_value = $_REQUEST["$auth_ext_xml_group_element_value"];
		}
		if (isset($_REQUEST["$auth_ext_xml_group_attribute"])) {
			$tikilib->set_preference("$auth_ext_xml_group_attribute", $_REQUEST["$auth_ext_xml_group_attribute"]);
			$$auth_ext_xml_group_attribute = $_REQUEST["$auth_ext_xml_group_attribute"];
		}
		if (isset($_REQUEST["$auth_ext_xml_group_attribute_value"])) {
			$tikilib->set_preference("$auth_ext_xml_group_attribute_value", $_REQUEST["$auth_ext_xml_group_attribute_value"]);
			$$auth_ext_xml_group_attribute_value = $_REQUEST["$auth_ext_xml_group_attribute_value"];
		}
		$groups['data'][$i]['auth_ext_xml_manage_group'] = $$auth_ext_xml_manage_group;
		$groups['data'][$i]['auth_ext_xml_group_element'] = $$auth_ext_xml_group_element;
		$groups['data'][$i]['auth_ext_xml_group_element_value'] = $$auth_ext_xml_group_element_value;
		$groups['data'][$i]['auth_ext_xml_group_attribute'] = $$auth_ext_xml_group_attribute;
		$groups['data'][$i]['auth_ext_xml_group_attribute_value'] = $$auth_ext_xml_group_attribute_value;
		$groups_array_set = true;
	}
	
}

// Get list of available languages
$languages = array();
$languages = $tikilib->list_languages();
$smarty->assign_by_ref("languages", $languages);

$smarty->assign("styles", $tikilib->list_styles());

$smarty->assign("available_languages", unserialize($tikilib->get_preference("available_languages")));
$smarty->assign("available_styles", unserialize($tikilib->get_preference("available_styles")));

$smarty->assign("userTracker", $tikilib->get_preference("userTracker", "n"));
$smarty->assign("groupTracker", $tikilib->get_preference("groupTracker", "n"));

global $trklib;
if (!is_object($trklib)) {
	require_once('lib/trackers/trackerlib.php');
}
$listTrackers = $trklib->list_trackers(0,-1,"name_desc","");
$smarty->assign("listTrackers",$listTrackers['list']);

if ($groups_array_set != true) {
	for ($i=0; $i<$group_arraylen; $i++) {
		$groupname = $groups['data'][$i]['groupName'];
		$auth_ext_xml_manage_group = 'auth_ext_xml_manage_'. $groupname;
		$auth_ext_xml_group_element = 'auth_ext_xml_element_' . $groupname;
		$auth_ext_xml_group_element_value = 'auth_ext_xml_element_val_' . $groupname;
		$auth_ext_xml_group_attribute = 'auth_ext_xml_attr_' . $groupname;
		$auth_ext_xml_group_attribute_value = 'auth_ext_xml_attr_val_' . $groupname;
		global $$auth_ext_xml_manage_group;
		global $$auth_ext_xml_group_element;
		global $$auth_ext_xml_group_element_value;
		global $$auth_ext_xml_group_attribute;
		global $$auth_ext_xml_group_attribute_value;
		$groups['data'][$i]['auth_ext_xml_manage_group'] = $$auth_ext_xml_manage_group;
		$groups['data'][$i]['auth_ext_xml_group_element'] = $$auth_ext_xml_group_element;
		$groups['data'][$i]['auth_ext_xml_group_element_value'] = $$auth_ext_xml_group_element_value;
		$groups['data'][$i]['auth_ext_xml_group_attribute'] = $$auth_ext_xml_group_attribute;
		$groups['data'][$i]['auth_ext_xml_group_attribute_value'] = $$auth_ext_xml_group_attribute_value;
	}
}
$smarty->assign('groups', $groups['data']);

$smarty->assign("change_theme", $tikilib->get_preference("change_theme", "n"));
$smarty->assign("change_language", $tikilib->get_preference("change_language", "n"));
$smarty->assign("rememberme", $tikilib->get_preference("rememberme", "disabled"));
$smarty->assign("remembertime", $tikilib->get_preference("remembertime", 7200));
$smarty->assign("allowRegister", $tikilib->get_preference("allowRegister", 'n'));
$smarty->assign("eponymousGroups", $tikilib->get_preference("eponymousGroups", 'n'));
$smarty->assign("useRegisterPasscode", $tikilib->get_preference("useRegisterPasscode", 'n'));
$smarty->assign("registerPasscode", $tikilib->get_preference("registerPasscode", ''));
$smarty->assign("validateUsers", $tikilib->get_preference("validateUsers", 'n'));
$smarty->assign("validateEmail", $tikilib->get_preference("validateEmail", 'n'));
$smarty->assign("forgotPass", $tikilib->get_preference("forgotPass", 'n'));
$smarty->assign("highlight_group", $tikilib->get_preference("highlight_group", ''));
$smarty->assign("listgroups", $listgroups = $userlib->list_all_groups());

ask_ticket('admin-inc-login');
?>
