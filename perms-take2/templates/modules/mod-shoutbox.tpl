{* $Id$ *}
{if $prefs.feature_shoutbox eq 'y' and $tiki_p_view_shoutbox eq 'y'}
{popup_init src="lib/overlib.js"}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Shoutbox{/tr}"}{/if}
  {tikimodule title=$tpl_module_title name="shoutbox" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
    {if $tiki_p_post_shoutbox eq 'y'}
      {if $prefs.feature_ajax == 'y'}{literal}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
	function submitShout() {
		var fm = xajax.$('shout_form');
		if ((verifyForm(fm) && xajax.$('shout_msg').value.length > 2) || 
				xajax.$('shout_remove').value || xajax.$('shout_edit').value) {
			xajax.$('shout_send').disabled=true;
			xajax.$('shout_send').value="{/literal}{$waittext}{literal}";
			xajax.config.requestURI = "tiki-shoutbox.php";
			xajax_processShout(xajax.getFormValues(fm), "{/literal}mod-shoutbox{$module_position}{$module_ord}{literal}");
			return false;
		} else {
			return true;
		}
	}
	function removeShout(inId) {
		if (confirm("{/literal}{tr}Are you sure you want to delete this shout?{/tr}{literal}")) {
			xajax.$('shout_remove').value = inId;
			return submitShout();
		} else {
			return true;
		}
	}
	function editShout(inId) {
		xajax.$('shout_edit').value = inId;
		return submitShout();
	}
//--><!]]>
</script>
      {/literal}{/if}
      {js_maxlength textarea=shout_msg maxlength=255}
      {if $prefs.feature_ajax != 'y'}<form action="{$shout_ownurl}" method="post" onsubmit="return verifyForm(this);" id="shout_form">{else}
      <form action="javascript:void(null);" onsubmit="return submitShout();" id="shout_form" name="shout_form">
      <input type="hidden" id="shout_remove" name="shout_remove" value="0" />
      <input type="hidden" id="shout_edit" name="shout_edit" value="0" />{/if}
	  {if !empty($shout_error)}<div class="highlight">{$shout_error}</div>{/if}
      <div align="center">
        <textarea rows="3" cols="16" class="tshoutbox" id="shout_msg" name="shout_msg"></textarea>
		{if $prefs.feature_antibot eq 'y' && $user eq ''}
			<table>{include file="antibot.tpl"}</table>
		{/if}
	    <input type="submit" id="shout_send" name="shout_send" value="{$buttontext}" />
      </div>
      </form>
    {/if}
    {section loop=$shout_msgs name=ix}
      <div class="shoutboxmodmsg">
        {assign var=userlink value=$shout_msgs[ix].user|userlink:"linkmodule"}
        {capture name=date}{strip} {* Print date *}
          {$shout_msgs[ix].timestamp|tiki_short_time}, {$shout_msgs[ix].timestamp|tiki_short_date}
        {/strip}{/capture}
	    {* Show user message in style according to 'tooltip' module parameter *}
	    {assign var=cdate value=$smarty.capture.date}
	    {if 0 and $tooltip == 1}{* TODO: Improve $userlink modifier one day to handle other attibutes better? *}
          <b>{strip}{$userlink|replace:" class=":" onmouseover='return overlib(\"$cdate\");' onmouseout='nd();' class="}{/strip}</b>:
        {else}
          <b>{strip}{$userlink}{/strip}</b>, {$cdate}:
        {/if}
        {$shout_msgs[ix].message}
        {if $tiki_p_admin_shoutbox eq 'y' || $user == $shout_msgs[ix].user }
          {if $prefs.feature_ajax == 'y'}
            [<a onclick="removeShout({$shout_msgs[ix].msgId});return false" href="#" class="linkmodule tips" title="|{tr}Delete this shout{/tr}">x</a>|<a href="tiki-shoutbox.php?msgId={$shout_msgs[ix].msgId}" class="linkmodule tips" title="|{tr}Edit this shout{/tr}">e</a>]
          {else}
            [<a href="{$shout_ownurl}shout_remove={$shout_msgs[ix].msgId}" class="linkmodule">x</a>|<a href="tiki-shoutbox.php?msgId={$shout_msgs[ix].msgId}" class="linkmodule">e</a>]
          {/if}
        {/if}
      </div>
    {/section}
    <div style="text-align: center">
      <a href="tiki-shoutbox.php" class="linkmodule">{tr}Read More{/tr}&hellip;</a>
    </div>
  {/tikimodule}
{/if}
