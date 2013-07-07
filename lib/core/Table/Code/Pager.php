<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Table_Code_Pager
 *
 * Creates code for the pager section of the Tablesorter code, including the code for ajax
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_Manager
 */
class Table_Code_Pager extends Table_Code_Manager
{

	public function setCode()
	{
		$p = '';
		if (isset($this->s['pager']) && $this->s['pager'] !== false) {
			$p[] = 'size: ' . $this->s['pager']['max'];
			$p[] = 'output: \'{startRow} to {endRow} ({totalRows})\'';
			$p[] = 'container: $(\'div#' . $this->s['pagercontrols']['id'] . '\')';
			$p[] = 'ajaxObject: {dataType: \'html\'}';
			$p[] = 'ajaxUrl : \'' . $this->s['pager']['ajax']['url'] . '\'';
			//ajax processing - this part grabs the html, usually from the smarty template file
			$ap = array(
				'var parsed = $.parseHTML( data );',
				'var parsedtable = $(parsed).find(\'table#' . $this->id . ' tbody\');',
				'var data = $(parsedtable).html();',
				'$(table).find(\'tbody\').html( data );',
				'var total = \'' . $this->s['total'] . '\';',
				'return [ total ];'
			);
			$p[] = $this->iterate($ap, 'ajaxProcessing: function(data, table){', $this->nt2 . '}',
				$this->nt3, '', '');
			//takes the url parameters generated by Tablesorter and converts to parameters that can
			//be used by Tiki
			$ca = array(
				'var vars = {}, hashes, hash, sort, filters, params = [], dir, newurl;',
				'hashes = url.slice(url.indexOf(\'?\') + 1).split(\'&\');',
				'for(var i = 0; i < hashes.length; i++) {',
				'	hash = hashes[i].split(\'=\');',
				'	vars[hash[0]] = hash[1];',
				'}',
				'sort = ' . json_encode($this->s['ajax']['sort']) . ';',
				'filters = ' . json_encode($this->s['ajax']['filters']) . ';',
				'$.each(vars, function(key, value) {',
				'	if (key in sort) {',
				'		if (value == 0){dir = \'_asc\'} else {dir = \'_desc\'};',
				'		params.push(sort[key] + dir);',
				'	}',
				'	if (key in filters) {',
				'		if (filters[key][value]){params.push(filters[key][value])} else {params.push(filters[key] + \'=\' + value)}',
				'	}',
				'});',
				'newurl = url.slice(0,url.indexOf(\'?\'));',
				'newurl = newurl + \'?offset=\' + (this.page * this.size) + \'&numrows=\' + this.size + \'&tsAjax=true\';',
				'$.each(params, function(key, value) {',
				'	newurl = newurl + \'&\' + value;',
				'});',
				'return newurl;'
			);
			$p[] = $this->iterate($ca, 'customAjaxUrl: function(table, url) {',  $this->nt2 . '}',
				$this->nt3, '', '');
		}

		$code = $this->iterate($p, '.tablesorterPager({', $this->nt . '});', $this->nt2, '');
		parent::$code[self::$level1] = $code;
	}
}