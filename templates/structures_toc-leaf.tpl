{strip}
{if $toc_type eq 'fancy' and $structure_tree.description}
	{$leafspace}
	<li class="fancytoclevel">
		{if $numbering}
			{$structure_tree.prefix}
		{/if}
		{if $showdesc}
			{$structure_tree.description} :
		{/if}
		<a href="{sefurl page=$structure_tree.pageName structure=$structurePageName page_ref_id=$structure_tree.page_ref_id}"
				class="link" title="{$structure_tree.description|escape}">
			{if $hilite}<b>{/if}
			{if $structure_tree.page_alias}
				{$structure_tree.page_alias}
			{else}
				{$structure_tree.pageName}
			{/if}
			{if $hilite}</b>{/if}
		</a>
		{if !$showdesc}: {$structure_tree.description}{/if}
	{* no </li> here *}
{else}
	{$leafspace}
	<li class="toclevel">
		{if $numbering}{$structure_tree.prefix} {/if}
		<a href="{sefurl page=$structure_tree.pageName structure=$structurePageName page_ref_id=$structure_tree.page_ref_id}"
			class="link" title="
			{if $showdesc}
				{if $structure_tree.page_alias}
					{$structure_tree.page_alias}
				{else}
					{$structure_tree.pageName}
				{/if}
			{else}
				{$structure_tree.description|escape}
			{/if}">
			{if $hilite}<b>{/if}
			{if $showdesc}
				{$structure_tree.description}
			{else}
				{if $structure_tree.page_alias}
					{$structure_tree.page_alias}
				{else}
					{$structure_tree.pageName}
				{/if}
			{/if}
			{if $hilite}</b>{/if}
		</a>
	{* no </li> here *}
{/if}
{/strip}