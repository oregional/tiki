{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-user_blogs.tpl,v 1.2 2004-01-16 18:38:29 musus Exp $ *}

{if $user}
    {if $feature_blogs eq 'y'}
	{tikimodule title="{tr}My blogs{/tr}" name="user_blogs"}
	<table border="0" cellpadding="0" cellspacing="0">
	{section name=ix loop=$modUserBlogs}
	    <tr class="module">{if $nonums != 'y'}<td valign="top">{$smarty.section.ix.index_next})</td>{/if}
	    <td><a class="linkmodule" href="tiki-view_blog.php?blogId={$modUserBlogs[ix].blogId}">{$modUserBlogs[ix].title}</a></td></tr>
	{/section}
	</table>
	{/tikimodule}
    {/if}
{/if}
