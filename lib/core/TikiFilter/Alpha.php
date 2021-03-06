<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiFilter_Alpha extends Zend\Filter\PregReplace
{
	private $filter;

	function __construct($space = false)
	{
		$space = is_bool($space) ? $space : false;
		$regexSpace = $space === true ? '\p{Zs}' : '';
		if (!extension_loaded('intl')) {
			$this->filter = null;
			// a stright copy from \Zend\I18n\Filter\Alpha::filter
			parent::__construct('/[^\p{L}' . $regexSpace . ']/u', '');
		} else {
			$this->filter = new \Zend\I18n\Filter\Alpha($space);
		}
	}

	function filter($value)
	{
		if (!extension_loaded('intl')) {
			return parent::filter($value);
		} else {
			return $this->filter->filter($value);
		}
	}
}
