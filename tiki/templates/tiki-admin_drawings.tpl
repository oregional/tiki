{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_drawings.tpl,v 1.6 2003-08-01 10:31:09 redflo Exp $ *}

<a class="pagetitle" href="tiki-admin_drawings.php">{tr}Edit drawings{/tr}</a><br /><br />

{if $preview eq 'y'}
<div align="center">
	<a href='#' onClick="javascript:window.open('tiki-editdrawing.php?path={$path}&amp;drawing={$draw_info.name}','','menubar=no,width=252,height=25');">
		<img width='154' height='98' border='0' src='img/wiki/{$draw_info.filename_draw}' alt='click to edit' />
	</a>
</div>
{/if}


<form method="post" action="tiki-admin_drawings.php">
<input type="hidden" name="ver" value="{$smarty.request.ver|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
{tr}Find{/tr}:<input type="text" name="find" value="{$find|escape}" />
</form>
<h3>{tr}Available drawings{/tr}:</h3>
<form method="post" action="tiki-admin_drawings.php">
<input type="hidden" name="ver" value="{$smarty.request.ver|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
{if $smarty.request.ver}
<td width="2%" class="heading"><input type="submit" name="del" value="{tr}x{/tr} " /></td>
{/if}
<td class="heading">{tr}Name{/tr}</td>
<td width="3%" class="heading">{tr}Ver{/tr}</a></td>
<td width="3%" class="heading">{tr}Versions{/tr}</a></td>
<td width="12%" class="heading">{tr}Action{/tr}</a></td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
{if $smarty.request.ver}
<td class="{cycle advance=false}">
<input type="checkbox" name="draw[{$items[user].drawId}]" />
</td>
{/if}
<td class="{cycle advance=false}">
{if $smarty.request.ver}
{$items[user].name}
{else}
<a href="tiki-admin_drawings.php?ver={$items[user].name}" class="link">
{$items[user].name}</a>
{/if}
</td>
<td style="text-align:right;" class="{cycle advance=false}">
{$items[user].version}
</td>
<td style="text-align:right;" class="{cycle advance=false}">
{$items[user].versions}
</td>
<td class="{cycle}">
{if $smarty.request.ver}
	<a href="tiki-admin_drawings.php?ver={$smarty.request.ver}&amp;remove={$items[user].drawId}" class="link"><img src='img/icons2/delete.gif' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' border='0' /></a>
{else}
	<a href="tiki-admin_drawings.php?ver={$smarty.request.ver}&amp;removeall={$items[user].name}" class="link"><img src='img/icons2/delete.gif' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' border='0' /></a>
{/if}
<a href="tiki-admin_drawings.php?ver={$smarty.request.ver}&amp;previewfile={$items[user].drawId}" class="link"><img src='img/icons/ico_img.gif' border='0' alt='{tr}view{/tr}' title='{tr}view{/tr}' /></a>
{if not $smarty.request.ver}
<a class="link" href='#' onClick="javascript:window.open('tiki-editdrawing.php?path={$path}&amp;drawing={$items[user].name}','','menubar=no,width=252,height=25');">
	<img src='img/icons/edit.gif' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' />
</a>
{/if}
</td>
</tr>
{sectionelse}
<tr><td colspan="4" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
</form>

<div class="mini">
<div align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_drawings.php?ver={$smart.request.ver}&amp;offset={$prev_offset}&amp;find={$find}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_drawings.php?ver={$smart.request.ver}&amp;offset={$next_offset}&amp;find={$find}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_drawings.php?offset={$selector_offset}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div> 
