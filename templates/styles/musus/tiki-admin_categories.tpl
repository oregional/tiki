{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-admin_categories.tpl,v 1.4 2004-01-26 03:28:34 musus Exp $ *}
<a class="pagetitle" href="tiki-admin_categories.php">{tr}Admin categories{/tr}</a>
<!-- the help link info -->  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=CategoryAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin categories{/tr}">{$helpIcon $helpIconDesc}</a>{/if}
<!-- link to tpl -->
      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_categories.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin categories tpl{/tr}"><img alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>{/if}

<!-- begin -->
<br />
<br />
<div class="tree" id="top">
<table class="tcategpath">
<tr>
  <td class="tdcategpath">{tr}Current category{/tr}: {$path} </td>
  <td class="tdcategpath" align="right">
  <table><tr><td>
  {* Don't show 'TOP' button if we already on TOP but reserve space to avoid visual effects on change view *}
  <div class="button2" style="visibility:{if $parentId ne '0'}visible{else}hidden{/if}">
      <a class="linkbut" href="tiki-browse_categories.php?parentId=0">{tr}top{/tr}</a>
  </div>
  </td></tr></table></td>
</tr>
</table>

{* Show tree *}
{ * If not TOP level, append '..' as first node :) *}
{if $parentId ne '0'}
<div class="treenode">
  <a class="catname" href="tiki-admin_categories.php?parentId={$father}" title="Upper level">..</a>
</div>
{/if}
{$tree}
</div>

<br />
<a name="editcreate"></a>
<table class="normalnoborder">
<tr>
  <td valign="top">
    <div class="tiki">
      <div class="tiki-title">
      {if $categId > 0}
      {tr}Edit this category:{/tr} {$name} [<a href="tiki-admin_categories.php?parentId={$parentId}#editcreate" class="tikitlink">{tr}create new{/tr}</a>]
      {else}
      {tr}Add new category{/tr}
      {/if}
      </div>
      <div class="tiki-content">
      <form action="tiki-admin_categories.php" method="post">
      <input type="hidden" name="categId" value="{$categId|escape}" />
      <table>
        <tr><td><label>{tr}Parent{/tr}:</label></td><td>
				<select name="parentId">
				<option value="0">{tr}top{/tr}</option>
				{section name=ix loop=$categories}
				<option value="{$categories[ix].categId|escape}" {if $categories[ix].categId eq $parentId}selected="selected"{/if}>{$categories[ix].name}</option>
				{/section}
				</select>
				</td></tr>
        <tr><td><label>{tr}Name{/tr}:</label></td><td><input type="text" name="name" value="{$name|escape}" /></td></tr>
        <tr><td><label>{tr}Description{/tr}:</label></td><td><textarea rows="4" cols="16" name="description">{$description|escape}</textarea></td></tr>
        <tr><td align="center" colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
      </table>
      </form>
      </div>
    </div>
  </td>
</tr>
</table>
<br />
<table class="normalnoborder">
<tr>
  <td valign="top">
    <div class="tiki">
      <div class="tiki-title">{tr}Objects in category{/tr}</div>
      <div class="tiki-content">
      <table class="findtable">
      <tr><td>{tr}Find{/tr}</td>
      <td>
        <form method="get" action="tiki-admin_categories.php">
        <input type="text" name="find" />
        <input type="hidden" name="parentId" value="{$parentId|escape}" />
        <input type="submit" value="{tr}find{/tr}" name="search" />
        <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
        <input type="hidden" name="find_objects" value="{$find_objects|escape}" />
        </form>
      </td>
      </tr>
      </table>
      <table>
      <tr>
        <th><a class="tableheading" href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></th>
        <th><a class="tableheading" href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></th>
        <th>&nbsp;</th>
      </tr>
      {section name=ix loop=$objects}
      <tr class="even">
        <td><a href="{$objects[ix].href}" title="{$objects[ix].name}">{$objects[ix].name|truncate:25:"(...)":true}</a></td>
        <td>{$objects[ix].type}</td>
        <td>[<a href="tiki-admin_categories.php?parentId={$parentId}&amp;removeObject={$objects[ix].catObjectId}&amp;fromCateg={$parentId}">{tr}x{/tr}</a>]</td>
      </tr>
      {/section}
      </table>      
      <div align="center">
        <div class="mini">
        {if $prev_offset >= 0}
          [<a class="prevnext" href="tiki-admin_categories.php?find={$find}&amp;parentId={$parentId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>] 
        {/if}
        {tr}Page{/tr}: {$actual_page}/{if $cant_pages eq 0}1{else}{$cant_pages}{/if}
        {if $next_offset >= 0}
           [<a class="prevnext" href="tiki-admin_categories.php?find={$find}&amp;parentId={$parentId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
        {/if}
        </div>
      </div>
      </div>
    </div>
  </td>
  </tr>
  </table>
  <br />
<table class="normalnoborder">
  <tr>
  <td valign="top">
    <div class="tiki">
      <div class="tiki-title">{tr}Add objects to category{/tr}</div>
      <div class="tiki-content">
      <table class="findtable">
      <tr><td>{tr}Find{/tr}</td>
      <td>
        <form method="get" action="tiki-admin_categories.php">
        <input type="text" name="find_objects" />
        <input type="hidden" name="parentId" value="{$parentId|escape}" />
        <input type="submit" value="{tr}filter{/tr}" name="search_objects" />
        <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
        <input type="hidden" name="offset" value="{$offset|escape}" />
        <input type="hidden" name="find" value="{$find|escape}" />
        </form>
      </td>
      </tr>
      </table>
      <form action="tiki-admin_categories.php" method="post">
      <input type="hidden" name="parentId" value="{$parentId|escape}" />
      <table>
        <tr>
          <td><label>{tr}page{/tr}:</label></td>
          <td><select name="pageName[]" multiple="multiple" size="5">{section name=ix loop=$pages}<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="addpage" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td><label>{tr}article{/tr}:</label></td>
          <td><select name="articleId">{section name=ix loop=$articles}<option value="{$articles[ix].articleId|escape}">{$articles[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="addarticle" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td><label>{tr}blog{/tr}:</label></td>
          <td><select name="blogId">{section name=ix loop=$blogs}<option value="{$blogs[ix].blogId|escape}">{$blogs[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="addblog" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td><label>{tr}directory{/tr}:</label></td>
          <td><select name="directoryId">{section name=ix loop=$directories}<option value="{$directories[ix].categId|escape}">{$directories[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="adddirectory" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td><label>{tr}image gal{/tr}:</label></td>
          <td><select name="galleryId">{section name=ix loop=$galleries}<option value="{$galleries[ix].galleryId|escape}">{$galleries[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="addgallery" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td><label>{tr}file gal{/tr}:</label></td>
          <td><select name="file_galleryId">{section name=ix loop=$file_galleries}<option value="{$file_galleries[ix].galleryId|escape}">{$file_galleries[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="addfilegallery" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td><label>{tr}forum{/tr}:</label></td>
          <td><select name="forumId">{section name=ix loop=$forums}<option value="{$forums[ix].forumId|escape}">{$forums[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="addforum" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td><label>{tr}poll{/tr}:</label></td>
          <td><select name="pollId">{section name=ix loop=$polls}<option value="{$polls[ix].pollId|escape}">{$polls[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="addpoll" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td><label>{tr}faq{/tr}:</label></td>
          <td><select name="faqId">{section name=ix loop=$faqs}<option value="{$faqs[ix].faqId|escape}">{$faqs[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="addfaq" value="{tr}add{/tr}" /></td>
        </tr>
	   <tr>
          <td><label>{tr}tracker{/tr}:</label></td>
          <td><select name="trackerId">{section name=ix loop=$trackers}<option value="{$trackers[ix].trackerId|escape}">{$trackers[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="addtracker" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td><label>{tr}quiz{/tr}:</label></td>
          <td><select name="quizId">{section name=ix loop=$quizzes}<option value="{$quizzes[ix].quizId|escape}">{$quizzes[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td><input type="submit" name="addquiz" value="{tr}add{/tr}" /></td>
        </tr>
      </table>
      </form>
      </div>
    </div>
  </td>
<tr>
</table>
