<a class="pagetitle" href="tiki-admin_cookies.php">{tr}Admin cookies{/tr}</a><br/>
<h2>{tr}Create/edit cookies{/tr}</h2>
<form action="tiki-admin_cookies.php" method="post">
<input type="hidden" name="cookieId" value="{$cookieId}" />
<table class="normal">
<tr><td class="formcolor">{tr}Cookie{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="cookie" value="{$cookie}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Upload Cookies from textfile{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-admin_cookies.php" method="post">
<table class="normal">
<tr><td class="formcolor">{tr}Upload from disk:{/tr}</td><td class="formcolor">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000000">
<input name="userfile1" type="file"></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="upload" value="{tr}upload{/tr}" /></td></tr>
</table>
</form>
<br/>

<h2>{tr}Cookies{/tr}</h2>
<a href="tiki-admin_cookies.php?removeall=1" class="link">Remove all cookies</a><br/><br/>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_cookies.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'cookieId_desc'}cookieId_asc{else}cookieId_desc{/if}">{tr}ID{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'cookie_desc'}cookie_asc{else}cookie_desc{/if}">{tr}cookie{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].cookieId}</td>
<td class="odd">{$channels[user].cookie}</td>
<td class="odd">
   <a class="link" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].cookieId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;cookieId={$channels[user].cookieId}">{tr}edit{/tr}</a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].cookieId}</td>
<td class="even">{$channels[user].cookie}</td>
<td class="even">
   <a class="link" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].cookieId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_cookies.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;cookieId={$channels[user].cookieId}">{tr}edit{/tr}</a>
</td>
</tr>
{/if}
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_cookies.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_cookies.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>

