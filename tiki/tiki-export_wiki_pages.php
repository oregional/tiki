<?php
include_once("tiki-setup_base.php");
include_once("lib/ziplib.php");
if($tiki_p_admin_wiki!='y') die;
if(!isset($_REQUEST["page"])) {
  $tikilib->MakeWikiZip();
  header("location: dump/export.tar");
} else {
  if(isset($_REQUEST["all"])) $all=0; else $all=1;
  $data = $tikilib->export_wiki_page($_REQUEST["page"],$all);
  $page=$_REQUEST["page"];
  header("Content-type: application/unknown");
  header( "Content-Disposition: inline; filename=$page" );
  echo $data;
  
}
?>