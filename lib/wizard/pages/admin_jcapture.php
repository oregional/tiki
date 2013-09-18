<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's jCapture handler 
 */
class AdminWizardJCapture extends Wizard 
{
	function onSetupPage ($homepageUrl) 
	{
		global	$smarty, $prefs;

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		$showPage = false;

		// Show if Auto TOC is selected
		if ($prefs['feature_jcapture'] === 'y') {
			$showPage = true;
		}
		
		// Assign the page tempalte
		$wizardTemplate = 'wizard/admin_jcapture.tpl';
		$smarty->assign('wizardBody', $wizardTemplate);
		
		return $showPage;
	}

	function onContinue () 
	{
		// Run the parent first
		parent::onContinue();
	}
}