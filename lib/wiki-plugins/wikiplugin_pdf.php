<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

function wikiplugin_pdf_info()
{
	//including prefs to set global print settings as default value of parameters
	global $prefs;
	 return array(
                'name' => 'PluginPDF',
                'documentation' => 'PluginPDF',
                'description' => tra('For customized pdf generation, to override global pdf settings.'),
                'tags' => array( 'basic' ),
                'prefs' => array( 'wikiplugin_pdf' ),
				'format' => 'html',
				'iconname' => 'pdf',
				'introduced' => 17,
                'params' => array(
					'printfriendly' => array(
						'name' => tra('Print Friendly PDF'),
						'description' => tra('Value:y/n. Enabling this option will change theme background color to white and text /headings color to black. If set to \'n\', theme colors will be retained in pdf'),
						'type' => 'flag',
						'default' => 'y'			
					),
					'orientation' => array(
						'name' => tra('PDF Orientation'),
						'description' => tra('Landscape or Portrait'),
						'type' => 'list',
						'default'=>$prefs['print_pdf_mpdf_orientation'],
						'options' => array(
							array('text'=>'Portrait','value'=>'P'),
							array('text'=>'Landscape','value'=>'L'),
						),
						
					),
					'pagesize' => array(
					'name' => tra('PDF page size'),
					'description' => tra('ISO Standard sizes: A0, A1, A2, A3, A4, A5 or North American paper sizes: Letter, Legal, Tabloid/Ledger (for ledger, select landscape orientation)'),
					'type' => 'list',
					'options' => array(
						array('text'=>'Letter','value'=>'Letter'),
						array('text'=>'Legal','value'=>'Legal'),
						array('text'=>'Tabloid/Ledger','value'=>'Tabloid/Ledger'),
						array('text'=>'A0','value'=>'A0'),
						array('text'=>'A1','value'=>'A1'),
						array('text'=>'A2','value'=>'A2'),
						array('text'=>'A3','value'=>'A3'),
						array('text'=>'A4','value'=>'A4'),
						array('text'=>'A5','value'=>'A5'),
						array('text'=>'A6','value'=>'A6')
						)
					),
					'header' => array(
						'name' => tra('PDF header text'),
						'description' => tra('Format: Left text| Center Text | Right Text. Possible values, custom text, {PAGENO},{PAGETITLE},{DATE j-m-Y}.'),
						'tags' => array('basic'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_header'],
						'shorthint'=>'Left text |Center Text| Right Text'
					),
					'footer' => array(
						'name' => tra('PDF footer text'),
						'description' => tra('Possible values, custom text, {PAGENO}, {DATE j-m-Y} For example:Document Title|Center Text|{PAGENO}'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_footer'],
					),
					'margin_left' => array(
						'name' => tra('Left margin'),
						'description' => tra('Numeric value.For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_left'],
						'size' => '2',
						'filter' => 'digits',
					),
					'margin_right' => array(
						'name' => tra('Right margin'),
						'description' => tra('Numeric value, no need to add px. For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_right'],
						'size' => '2',
						'filter' => 'digits',
					),
					'margin_top' => array(
						'name' => tra('Top margin'),
						'description' => tra('Numeric value, no need to add px. For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_top'],
						'size' => '2',
						'filter' => 'digits',
					),
					'margin_bottom' => array(
						'name' => tra('Bottom margin'),
						'description' => tra('Numeric value, no need to add px. For example 10'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_bottom'],
						'size' => '2',
						'filter' => 'digits',
					),
					'margin_header' => array(
						'name' => tra('Header margin from top of document'),
						'description' => tra('Only applicable if header is set. Numeric value only, no need to add px.Warning: Header can overlap text if top margin is not set properly'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_header'],
						'size' => '2',
						'filter' => 'digits',
						
					),
					'margin_footer' => array(
						'name' => tra('Footer margin from bottom of document'),
						'description' => tra('Only applicable if footer is set.Numeric value only, no need to add px. Warning: Footer can overlap text if bottom margin is not set properly'),
						'type' => 'text',
						'default' => $prefs['print_pdf_mpdf_margin_footer'],
						'size' => '2',
						'filter' => 'digits',
						
					),
					'password' => array(
						'name' => tra('PDF password for viewing'),
						'description' => tra('Secure confidential PDF with password, leave blank if password protected is not needed'),
						'type' => 'password',
						'default' => $prefs['print_pdf_mpdf_password'],
					)
                ),
        );
}

function wikiplugin_pdf($data, $params)
{
	//included globals to check mpdf selection as pdf generation engine
	global $prefs;
	
	//checking if mdpf is default pdf generation engine, since plugin is only set for mpdf. 
	if($prefs['print_pdf_from_url'] != 'mpdf')
		return WikiParser_PluginOutput::internalError(tr('For pluginPDF, please select mpdf as default PDF engine from Print Settings.'));
	$paramList='';
	//creating string of data paramaters set by user
 	foreach($params as $paramName=>$param)
	{
		$paramList.=$paramName."='".$param."' ";
	}
	return "<pdfsettings ".$paramList."></pdfsettings>";
}