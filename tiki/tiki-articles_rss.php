<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-articles_rss.php,v 1.17 2003-10-11 11:51:09 ohertel Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/tikilib.php');

// object specific things:
if ($rss_articles != 'y') {
	die; // TODO: output of rss file with message: rss disabled
}

if ($tiki_p_read_article != 'y') {
	$smarty -> assign('msg', tra("Permission denied you cannot view this section"));
	$smarty -> display("styles/$style_base/error.tpl");
	die; // TODO: output of rss file with message: permission denied
}

$title = "Tiki RSS feed for articles";
$desc = "Last articles.";
$now = date("U");
$changes = $tikilib -> list_articles(0, $max_rss_articles, 'publishDate_desc', '', $now, $user);

// --- object independend things: (TODO: cleaning up not yet finished)

$rss_use_css = false; // default is: do not use css
if (isset($_REQUEST["css"])) {
	$rss_use_css = true;
}

$rss_version = 1; // default is: rss v1.0 - TODO: make this configurable
if (isset($_REQUEST["ver"]))
	if (substr($_REQUEST["ver"],0,1) == '2') {
		$rss_version = 2;
	}

$url = $_SERVER["REQUEST_URI"];
$url = substr($url, 0, strpos($url."?", "?")); // strip all parameters from url
$urlarray = parse_url($url);

$pagename = substr($urlarray["path"], strrpos($urlarray["path"], '/') + 1);

$home = httpPrefix().str_replace($pagename, $tikiIndex, $urlarray["path"]);
$img = httpPrefix().str_replace($pagename, "img/tiki.jpg", $urlarray["path"]);
$read = httpPrefix().str_replace($pagename, "tiki-read_article.php?articleId=", $urlarray["path"]);
$url = httpPrefix().$url;

$css = httpPrefix().str_replace($pagename, "lib/rss/rss-style.css", $urlarray["path"]);

// --- output starts here 
header("content-type: text/xml");
print '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
print '<!--  RSS generated by TikiWiki CMS (www.tikiwiki.org) on '.date('r').' -->'."\n";

if ($rss_use_css) {
	print '<?xml-stylesheet href="'.htmlspecialchars($css).'" type="text/css"?>'."\n";
}

if ($rss_version == 2) {
	print '<rss version="2.0">'."\n";
	print "<channel>\n";
} else {
	print '<rdf:RDF xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:h="http://www.w3.org/1999/xhtml" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/">'."\n";
	print '<channel rdf:about="'.htmlspecialchars($url).'">'."\n";
}

print "<title>".htmlspecialchars($title)."</title>\n";
print "<link>".htmlspecialchars($home)."</link>\n";
print "<description>".htmlspecialchars($desc)."</description>\n";

if ($rss_version == 2) {
	print "<language>en-us</language>\n";
} // TODO: make language configurable

print "\n";

if ($rss_version == 2) {
	print '<image>'."\n";
	print "<title>".htmlspecialchars($title)."</title>\n";
	print "<link>".htmlspecialchars($home)."</link>\n";
	print "<url>".htmlspecialchars($url)."</url>\n";
	print "</image>\n\n";
}

if ($rss_version == 1) {
	print "<items>\n";
	print "<rdf:Seq>\n";
	// LOOP collecting last changes to the articles (index)
	foreach ($changes["data"] as $chg) {
		print ('        <rdf:li resource="'.htmlspecialchars($read.$chg["articleId"]).'" />'."\n");
	}
	print "</rdf:Seq>\n";
	print "</items>\n";

	print "</channel>\n";
}

// LOOP collecting last changes to the articles
foreach ($changes["data"] as $chg) {
	if ($rss_version == 2) {
		print ("<item>\n");
	} else {
		print ('<item rdf:about="'.htmlspecialchars($read.$chg["articleId"]).'">'."\n");
	}
	print ('  <title>'.htmlspecialchars($chg["articleId"]).'</title>'."\n");
	print ('  <link>'.htmlspecialchars($read.$chg["articleId"]).'</link>'."\n");

  $date = $tikilib -> date_format($tikilib -> get_short_datetime_format(), $chg["publishDate"]);
	if ($rss_version == 2) {
		print ('<description>'.htmlspecialchars($chg["heading"]).'</description>'."\n");
		// print("<author>".htmlspecialchars($chg["user"])."</author>\n"); // TODO: email address of author
		print ('<guid isPermaLink="true">'.htmlspecialchars($read.$chg["articleId"]).'</guid>'."\n");
		print ("<pubDate>".htmlspecialchars($date)."</pubDate>\n");
	} else {
		print ('  <description>'.htmlspecialchars($chg["heading"]).'</description>'."\n");
	}
	print ('</item>'."\n\n");
}

if ($rss_version == 2) {
	print "</channel>\n";
	print "</rss>\n";
} else {
	print "</rdf:RDF>\n";
}
?>