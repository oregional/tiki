<div class="forumspagetitle">
<a class="forumspagetitle" href="tiki-forums.php">Forums</a>
</div>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-forums.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="forumstable">
<tr>
<td width="50%" class="forumheading"><a class="lforumheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="forumheading"><a class="lforumheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'threads_desc'}threads_asc{else}threads_desc{/if}">{tr}topics{/tr}</a></td>
<td class="forumheading"><a class="lforumheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'comments_desc'}comments_asc{else}comments_desc{/if}">{tr}posts{/tr}</a></td>
<!--<td class="forumheading">{tr}users{/tr}</td>-->
<!--<td class="forumheading">{tr}age{/tr}</td>-->
<td class="forumheading">{tr}ppd{/tr}</td>
<td class="forumheading"><a class="lforumheading" href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastPost_desc'}lastPost_asc{else}lastPost_desc{/if}">{tr}last post{/tr}</a></td>
<td class="forumheading"><a class="lforumheading href="tiki-admin_forums.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}visits{/tr}</a></td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
{if ($channels[user].individual eq 'n') or ($tiki_p_admin eq 'y') or ($channels[user].individual_tiki_p_forum_read eq 'y')}
<td class="forumstableodd"><a class="forumname" href="tiki-view_forum.php?forumId={$channels[user].forumId}">{$channels[user].name}</a>
{else}
<td class="forumstableodd">{$channels[user].name}
{/if}
{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
<a class="admlink" href="tiki-admin_forums.php?forumId={$channels[user].forumId}">admin</a>
{/if}
</td>
<td class="forumstableinfoodd">{$channels[user].threads}</td>
<td class="forumstableinfoodd">{$channels[user].comments}</td>
<!--<td class="forumstableinfodd">{$channels[user].users}</td> -->
<!--<td class="forumstableinfoodd">{$channels[user].age}</td> -->
<td class="forumstableinfoodd">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
<td class="forumstableinfoodd">{$channels[user].lastPost|date_format:"%d of %b [%H:%M]"}</td>
<td class="forumstableinfoodd">{$channels[user].hits}</td>
</tr>
{else}
<tr>
{if ($channels[user].individual eq 'n') or ($tiki_p_admin eq 'y') or ($channels[user].individual_tiki_p_forum_read eq 'y')}
<td class="forumstableeven"><a class="forumname" href="tiki-view_forum.php?forumId={$channels[user].forumId}">{$channels[user].name}</a>
{else}
<td class="forumstableeven">{$channels[user].name}
{/if}
{if ($tiki_p_admin eq 'y') or (($channels[user].individual eq 'n') and ($tiki_p_admin_forum eq 'y')) or ($channels[user].individual_tiki_p_admin_forum eq 'y')}
<a class="admlink" href="tiki-admin_forums.php?forumId={$channels[user].forumId}">admin</a>
{/if}
</td>
<td class="forumstableinfoeven">{$channels[user].threads}</td>
<td class="forumstableinfoeven">{$channels[user].comments}</td>
<!--<td class="forumstableinfoeven">{$channels[user].users}</td>-->
<!--<td class="forumstableinfoeven">{$channels[user].age}</td> -->
<td class="forumstableinfoeven">{$channels[user].posts_per_day|string_format:"%.2f"}</td>
<td class="forumstableinfoeven">{$channels[user].lastPost|date_format:"%d of %b [%H:%M]"}</td>
<td class="forumstableinfoeven">{$channels[user].hits}</td>
</tr>
{/if}
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="forumprevnext" href="tiki-admin_forums.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="forumprevnext" href="tiki-admin_forums.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
</div>
</div>

