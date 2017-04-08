<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
 * Class Table_Plugin
 *
 * For use by plugins to apply tablesorter to tables generated by the plugin. Creates parameters that
 * can be merged in with the plugin's parameters and generates settings from user input that can be
 * used in Table_Factory
 */
class Table_Plugin
{
	/**
	 * Standard tablesorter parameters for a plugin
	 * @var array
	 */
	public $params = array();

	/**
	 * Holds the settings created by setSettings function below
	 * @var
	 */
	public $settings;

	/**
	 * Available types of column and table calculations
	 * @var array
	 */
	private $mathtypes = ['count', 'sum', 'max', 'min', 'mean', 'median', 'mode', 'range', 'varp', 'vars', 'stdevp',
			'stdevs'];

	/**
	 * Map to actual tablesorter syntax
	 * @var array
	 */
	private $totalfilters = [
		'visible' => '',
		'unfiltered' => ':not(.filtered)',
		'all' => '*',
		'hidden' => ':hidden',
	];

	/**
	 * Creates parameters that can be appended to a plugin's native parameters so the user can
	 * set tablesorter functionality
	 */
	public function createParams()
	{
		$this->params = array(
			'server' => array(
				'required' => false,
				'name' => tra('Server-side Processing'),
				'description' => tr(
					'Enter %0y%1 to have the server do the sorting and filtering through Ajax and %0n%1 to have the
					browser do it (n is the default). Set to %0y%1 (and also set the Paginate parameter
					(%0tspaginate%1)) if you do not want all rows fetched at once, but rather fetch rows as you
					paginate, filter or sort.', '<code>', '</code>'
				),
				'since' => '12.0',
				'doctype' => 'tablesorter',
				'default' => 'n',
				'filter' => 'striptags',
			),
			'sortable' => array(
				'required' => false,
				'name' => tra('Overall Sort Settings'),
				'description' => tr(
					'Serves as the overall switch for turning jQuery Tablesorter on (also for filtering) as well as
					overall sort settings. Enter %0y%1 to allow sorting and %0n%1 to disallow (n is the default).
					Enter %0type:save%1 to allow sorts to be saved between page refreshes.
					Enter %0type:%2reset%3;text:*****%1 to allow sorting and show an unsort button with
					custom text. Enter %0type:%2savereset%3;text:buttontext%1 to allow the same for saved sorts.',
					'<code>', '</code>', '<strong>', '</strong>'
				),
				'since' => '12.0',
				'doctype' => 'tablesorter',
				'default' => 'n',
				'filter' => 'striptags',
			),
			'sortList' => array(
				'required' => false,
				'name' => tra('Pre-sorted Columns'),
				'description' => tr(
					'Bracketed numbers for column number (first column = 0) and sort direction
					(%20%3 = ascending, %21%3 = descending, %2n%3 = no sort, %2y%3 = allow sorting but no pre-sort),
					for example: %0. If the first pre-sorted or no filter column is not the first column, then you
					should use the %2y%3 parameter (as in %1) to assign all previous columns.',
					'<code>~np~[0,y],[1,0],[2,n]~/np~</code>', '<code>~np~[0,y]~/np~</code>', '<code>', '</code>'
				),
				'since' => '12.0',
				'doctype' => 'tablesorter',
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'tsortcolumns' => array(
				'required' => false,
				'name' => tra('Sort Settings by Column'),
				'description' => tr(
					'Set %0 and %1 settings for each column, using %2 to separate columns. To show group headings upon
					page load, the Pre-sorted Columns parameter (%3) will need to be set for a column with a group
					setting. Group will not work in plugins where the Server Side Processing parameter (%4) is set to
					%5.', '<code>type</code>', '<code>group</code>', '<code>|</code>', '<code>0sortList</code>',
					'<code>server</code>', '<code>y</code>')
					. '<br>' . tr('Set %0 to one of the following:', '<code>type</code>') . ' <code>text</code>,
					<code>digit</code>, <code>currency</code>, <code>percent</code>, <code>usLongDate</code>,
					<code>shortDate</code>, <code>isoDate</code>, <code>dateFormat-ddmmyyyy</code>,
					<code>ipAddress</code>, <code>url</code>, <code>time</code>', '<code>nosort</code>'
					. '<br>' . tr('Also handle strings in numeric columns with:') . ' <code>string-min</code>,
					</code>string-max</code>' .  tr('Handle empty cells with:') . ' <code>empty-top<code>,
					<code>empty-bottom</code>,  <code>empty-zero</code>.<br>'
					. tr('%0 creates automatic row headings upon sort with the heading text determined by
					the setting as follows: %1 (first letter), %2 (first word)', '<code>group</code>',
					'<code>letter</code>', '<code>word</code>') . ', <code>number</code>, <code>date</code>,
					<code>date-year</code>, <code>date-month</code>, <code>date-day</code>, <code>date-week</code>,
					<code>date-time</code>.' .  tr('%0 and %1 can be extended, e.g., %2 shows the first 2 words.
					%3 will group rows in blocks of ten. Group will not work in plugins where the Server Side Processing
					parameter (%4) is set to %5.', '<code>letter</code>', '<code>word</code>',
					'<code>word-2</code>', '<code>number-10</code>', '<code>server</code>', '<code>y</code>'
				),
				'since' => '12.0',
				'doctype' => 'tablesorter',
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'tsfilters' => array(
				'required' => false,
				'name' => tra('Column Filters'),
				'description' => tr(
					'Enter %0 for a blank text filter on all columns, or %1 for no filters. Or set custom column
					filters separated by %2 for each column for the following filter choices and parameters:',
					'<code>y</code>', '<code>n</code>', '<code>|</code>'
				)
					. '<br> <b>Text - </b><code>type:text;placeholder:xxxx</code><br>' .
					tra('(For PluginTrackerlist this will be an exact search, for other plugins partial values will work.)') . '<br>
					<b>Dropdown - </b><code>type:dropdown;placeholder:****;empty:****;option:****;option:****;option:****</code> <br>' .
					tr('Options generated automatically if not set and the %0server%1 parameter is not %0y%1.', '<code>', '</code>') . '<br>' .
					tr('Use %0value=Display label%1 to have the option value be different than the displayed label in
					the dropdown.', '<code>', '</code>') . '<br>' .
					tr('Use %0empty:Display label%1 to include an option with the specified label that will filter only empty rows.
					Only used if other options are not specified manually.', '<code>', '</code>') . '<br>
					<b>' . tra('Date range - ') . '</b><code>type:date;format:yy-mm-dd;from:2013-06-30;to:2020-12-31</code><br>' .
					tra('(from and to values set defaults for these fields when user clicks on the input field)') .
					tra('Beware that items with empty date values will not be shown when default date range filters are applied.') . '<br>
					<b>' . tra('Numeric range - ') . '</b><code>type:range;from:0;to:50</code><br>
					<b>' . tra('No filter - ') . '</b><code>type:nofilter</code><br>' .
					tr(
						'For example: %0tsfilters="type:dropdown;placeholder:Type to filter..."%1 would result in a dropdown
						filter on the first column with all unique values in that column in the dropdown list.'
						, '<code>', '</code>'),
				'since' => '12.0',
				'doctype' => 'tablesorter',
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'tsfilteroptions' => array(
				'required' => false,
				'name' => tra('Filter Options'),
				'description' => tr(
					'The following options are available: %0reset%1 (adds button to take off filters), and %0hide%1
					(Filters are revealed upon mouseover. Hide doesn\'t work when date and range filters are used.).
					To use both, set %0tsfilteroptions="type:reset;text:button text;style:hide"%1', '<code>', '</code>'
				),
				'since' => '12.0',
				'doctype' => 'tablesorter',
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'tspaginate' => array(
				'required' => false,
				'name' => tra('Paginate'),
				'description' => tr(
					'Enter %0y%1 to set default values based on the site setting for maximum records in listings (on the
				 	pagination table of the Look & Feel admin panel). Set to %0n%1 (and %0server%1 cannot be set to
				 	%0y%1) for no pagination. Set custom values as in the following example: ',
						'<code>', '</code>') .
					'<code>max:40;expand:60;expand:100;expand:140</code>',
				'since' => '12.0',
				'doctype' => 'tablesorter',
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'tscolselect' => array(
				'required' => false,
				'name' => tra('Column Select'),
				'description' => tr(
					'Add a button for hiding and re-showing columns. Also sets priority for dropping columns when
				 	browser is too narrow. Set each column to a number between 1 and 6 (1 is highest priority and last
				 	to be dropped) or to %0critical%1 to never hide or drop. An example with 4 columns:',
						'<code>', '</code>') .
					'<code>tscolselect="critical|4|5|6"</code>',
				'since' => '14.0',
				'doctype' => 'tablesorter',
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'tstotals' => array(
				'required' => false,
				'name' => tra('Totals'),
				'description' => tr('Generate table, column or row totals and set labels, using either %0 or the following
					syntax for each total: %1.', '<code>y</code>',
					'<code>type:value;formula:value;filter:value;label:value</code>')
					. '<br>' . tr('Setting to %0 will add one column total row set as follows: %1.', '(<code>y</code>)',
					'<code>type:col;formula:sum;filter:visible;label:Totals</code>')
					. '<br>' . tr('Separate multiple total row or column settings with a pipe %0. Set %1 only to
					generate sums of visible values. In all cases, cells in columns set to be ignored in
					the %2 parameter will not be included in calculations.', '(<code>|</code>)', '<code>type</code>',
					'<code>tstotaloptions</code>')
					.  '<br>' . tr('Instructions for each total option follows:')
					. '<br><strong>type</strong> - ' . tr('Choices are %0, for a row of columns totals, %1, for a
					column of row totals, and %2 to include amounts from all cells in the table body in a row total.',
					'<code>col</code>', '<code>row</code>', '<code>all</code>', '<code>tstotaloptions</code>')
					. '<br><strong>formula</strong> - ' . tr('set what the calculation is. Choices are:')
					. ' <code>sum</code>, <code>count</code>, <code>max</code>, <code>min</code>, <code>mean</code>,
					<code>median</code>, <code>mode</code>, <code>range</code>, <code>varp</code>, <code>vars</code>,
					<code>stdevp</code>, <code>stdevs</code>. ' . tr('Click %0 for a description of these options.',
					'<a href="http://mottie.github.io/tablesorter/docs/example-widget-math.html#attribute_settings">here</a>')
					. '<br><strong>filter</strong> - ' . tr('Determines the rows that will be included in the
					calculations (so no impact if %0). Also, when %1, only visible cells are included regardless of this
					setting. Choices are %2 (rows visible on the page), %3 (all rows not filtered out, even if not
					visible because of pagination), %4 (all rows, even if filtered or hidden), and %5 (rows filtered out
					and rows hidden due to pagination).', '<code>type:row</code>', '<code>server="y"</code>',
					'<code>visible</code>', '<code>unfiltered</code>', '<code>all</code>', '<code>hidden</code>')
					. '<br><strong>label</strong> - ' . tr('set the label for the total, which will appear in the header
					for row totals and in the first column for column totals.'),
				'since' => '15.0',
				'doctype' => 'tablesorter',
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
			),
			'tstotalformat' => array(
				'required' => false,
				'name' => tra('Total Format'),
				'description' => tr('Format for table totals (click %0 for patterns). Example:',
					'<a href="http://mottie.github.io/tablesorter/docs/example-widget-math.html#mask_examples">here</a>')
					. ' <code>#,###.</code><br>',
			),
			'tstotaloptions' => array(
				'required' => false,
				'name' => tra('Total Options'),
				'description' => tr('Pipe-separated options for totals for each column which are set in the %0 parameter:',
					'<code>tstotals</code>') . '<br><strong>format</strong> - '
					. tr('overrides the default number format set in %0', 'tstotalformat') . '<br>'
					. '<strong>ignore</strong> - ' . tr('column will be excluded from total calculations set in the %0
					parameter. Remember to include any columns that will be added for row totals set in the %0
					parameter.', '<code>tstotals</code>') . '<br>' . tr('Example:') . '<code>ignore|ignore|#,###.</code>',
				'since' => '15.0',
				'doctype' => 'tablesorter',
				'default' => '',
				'filter' => 'striptags',
				'advanced' => true,
			),
		);
	}

	/**
	* To be used within plugin program to convert user parameter settings into the settings array
	 * that can be used by Table_Factory to generate the necessary jQuery
	 *
	 * @param null $id                  //html element id for table and surrounding div
	 * @param string $server            //see params above
	 * @param string $sortable          //see params above
	 * @param null $sortList            //see params above
	 * @param null $tsortcolumns        //see params above
	 * @param null $tsfilters           //see params above
	 * @param null $tsfilteroptions     //see params above
	 * @param null $tspaginate          //see params above
	 * @param null $tscolselect         //see params above
	 * @param null $ajaxurl             //only needed if ajax will be used to pull partial record sets
	 * @param null $totalrows           //only needed if ajax will be used to pull partial record sets
	 * @param null $tstotals            //see params above
	 * @param null $tstotaloptions      //see params above
	 */
	public function setSettings ($id = null, $server = 'n', $sortable = 'n', $sortList = null, $tsortcolumns = null,
		$tsfilters = null, $tsfilteroptions = null, $tspaginate = null, $tscolselect = null, $ajaxurl = null,
		$totalrows = null, $tstotals = null, $tstotalformat = null, $tstotaloptions = null)
	{
		$s = array();

		//id
		if (!empty($id)) {
			$s['id'] = $id;
		}

		//sortable
		switch ($sortable) {
			case 'y':
			case 'server':
				$s['sorts']['type'] = true;
				break;
			case 'n':
				$s['sorts']['type'] = false;
				break;
			default:
				$sp = Table_Check::parseParam($sortable);
				if (isset($sp[0]['type'])) {
					$s['sorts']['type'] = $sp[0]['type'];
				}
		}

		//sortlist
		if (!empty($sortList) && (!isset($s['sorts']['type']) || $s['sorts']['type'] !== false)) {
			$crop = substr($sortList, 1);
			$crop = substr($crop, 0, -1);
			$slarray = explode('],[', $crop);
			if (is_array($slarray)) {
				foreach ($slarray as $l) {
					$lpieces = explode(',', $l);
					if (isset($lpieces[1])) {
						switch ($lpieces[1]) {
							case '0':
								$dir = 'asc';
								break;
							case '1':
								$dir = 'desc';
								break;
							case 'y':
								$dir = true;
								break;
							case 'n':
								$dir = false;
								break;
							default:
								if($s['sorts']['type'] !== false) {
									$dir = true;
								} else {
									$dir = false;
								}
						}
						if ($dir === false || $dir === true) {
							$s['columns'][$lpieces[0]]['sort']['type'] = $dir;
						} else {
							$s['columns'][$lpieces[0]]['sort']['dir'] = $dir;
						}
					}
				}
			}
		}

		//tsortcolumns
		if (!empty($tsortcolumns)) {
			$tsc = Table_Check::parseParam($tsortcolumns);
			if (is_array($tsc)) {
				foreach ($tsc as $col => $sortinfo) {
					if (isset($sortinfo['type']) && $sortinfo['type'] == 'nosort') {
						$sortinfo['type'] = false;
					}
					if (isset($s['columns'][$col]['sort'])) {
						$s['columns'][$col]['sort'] = $s['columns'][$col]['sort'] + $sortinfo;
					} else {
						$s['columns'][$col]['sort'] = $sortinfo;
					}
				}
				ksort($s['columns']);
			}
			if ($server === 'y') {
				$s['sorts']['group'] = false;
			}
		} else {
			$s['sorts']['group'] = false;
		}

		//tsfilters
		if (!empty($tsfilters)) {
			switch ($tsfilters) {
				case 'y':
					$s['filters']['type'] = 'text';
					break;
				case 'n':
					$s['filters']['type'] = false;
					break;
				default:
					$tsf = Table_Check::parseParam($tsfilters);
					if (is_array($tsf)) {
						foreach ($tsf as $col => $filterinfo) {
							if (isset($filterinfo) && $filterinfo['type'] === 'dropdown'
								&& !empty($filterinfo['options'])) {
								foreach ($filterinfo['options'] as $key => $value) {
									$filterinfo['options'][$key] = str_replace('=', '|', $value);
								}
							}
							if (isset($s['columns'][$col]['filter'])) {
								$s['columns'][$col]['filter'] = $s['columns'][$col]['filter'] + $filterinfo;
							} else {
								$s['columns'][$col]['filter'] = $filterinfo;
							}
						}
					}
			}
		}

		//tsfilteroptions
		if (!empty($tsfilteroptions) && !empty($s['filters']['type'])) {
			$tsfo = Table_Check::parseParam($tsfilteroptions);
			switch ($tsfo[0]['type']) {
				case 'reset':
					$s['filters']['type'] = 'reset';
					break;
				case 'hide':
					$s['filters']['hide'] = true;
					break;
			}
		}

		//tspaginate
		if (empty($tspaginate)) {
			$tspaginate = $server === 'y' ? 'y' : '';
		}
		if (!empty($tspaginate)) {
			$tsp = Table_Check::parseParam($tspaginate);
			//pagination must be on if server side processing is on ($server == 'y')
			if (is_array($tsp[0]) || $tsp[0] !== 'n' || ($tsp[0] === 'n' && $server === 'y')) {
				if (is_array($tsp[0])) {
					$s['pager'] = $tsp[0];
					if (isset($s['pager']['expand']) && is_array($s['pager']['expand'])) {
						if (isset($s['pager']['max']) && $s['pager']['max'] > 0) {
							$s['pager']['expand'] = array_merge(array($s['pager']['max']), $s['pager']['expand']);
						} else {
							$s['pager']['max'] = min($s['pager']['expand']);
						}
						$s['pager']['expand'] = array_unique($s['pager']['expand']);
						sort($s['pager']['expand']);
					}
				}
				$s['pager']['type'] = true;
			} elseif ($tsp[0] === 'n' && $server === 'n') {
				$s['pager']['type'] = false;
			}
		}

		//tscolselect
		if (!empty($tscolselect)) {
			$tscs = Table_Check::parseParam($tscolselect);
			if (is_array($tscs)) {
				$s['colselect']['type'] = true;
				foreach ($tscs as $col => $priority) {
					$s['columns'][$col]['priority'] = $priority;
				}
			}
		}

		//ajaxurl
		if (!empty($ajaxurl) && $server === 'y') {
			$url = $this->getAjaxurl($ajaxurl);
			$s['ajax']['url']['file'] = $url['path'];
			$s['ajax']['url']['query'] = $url['query'];
			$s['ajax']['type'] = true;
		} else {
			$s['ajax']['type'] = false;
		}

		//totalrows
		if (!empty($totalrows)) {
			$s['total'] = $totalrows;
		}

		//tstotals
		if (!empty($tstotals)) {
			if (trim($tstotals) === 'y') {
				$tstotals = 'type:col;formula:sum;label:' . tr('Page totals');
			}
			$tst = Table_Check::parseParam($tstotals);
			if (is_array($tst)) {
				foreach ($tst as $key => $tinfo) {
					if (!empty($tinfo['type'] && in_array($tinfo['type'], ['col', 'row', 'all']))) {
						$s['math']['totals'][$tinfo['type']][$key]['formula'] = !empty($tinfo['formula'])
							&& in_array($tinfo['formula'], $this->mathtypes) ? $tinfo['formula'] : 'sum';
						if (!empty($tinfo['filter']) && isset($this->totalfilters[$tinfo['filter']])) {
							if ($server === 'y') {
								$s['math']['totals'][$tinfo['type']][$key]['filter'] = '';
								$labelfilter = '';
							} else {
								$s['math']['totals'][$tinfo['type']][$key]['filter'] = $this->totalfilters[$tinfo['filter']];
								$labelfilter = $tinfo['filter'];
							}
						} else {
							$s['math']['totals'][$tinfo['type']][$key]['filter'] = '';
							$labelfilter = '';
						}
						if (isset($tinfo['label'])) {
							$s['math']['totals'][$tinfo['type']][$key]['label'] = $tinfo['label'];
						} else {
							$map = ['col' => 'Column', 'row' => 'Row', 'all' => 'Table'];
							$label = $map[$tinfo['type']] . ' '
								. $s['math']['totals'][$tinfo['type']][$key]['formula'] . ' ' . $labelfilter;
							$s['math']['totals'][$tinfo['type']][$key]['label'] = tr($label);
						}
					}
				}
			}
		}

		//tstotalformat
		if (!empty($tstotalformat)) {
			$s['math']['format'] = $tstotalformat;
		}

		//tstotaloptions
		if (!empty($tstotaloptions)) {
			$tsto = Table_Check::parseParam($tstotaloptions);
			if (is_array($tsto)) {
				foreach($tsto as $col => $option) {
					if ($option === 'ignore') {
						$s['columns'][$col]['math']['ignore'] = true;
					//only other option is format
					} elseif (!empty($option)) {
						$s['columns'][$col]['math']['format'] = $option['format'];
					}
				}
			}
		}

		$this->settings = $s;

	}

	/**
	 * Utility to add ajax parameters to URL
	 *
	 * @param $ajaxurl
	 *
	 * @return string
	 */
	private function getAjaxurl($ajaxurl)
	{
		$str = '{sort:sort}&{filter:filter}';
		$url = parse_url($ajaxurl);
		if (isset($url['query'])) {
			$url['query'] = '?' .  $url['query'] . '&' . $str;
		} else {
			$url['query'] = '?' . $str;
		}
		return $url;
	}

}
