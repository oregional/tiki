{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-top_visited_faqs.tpl,v 1.8 2005-03-12 16:51:00 mose Exp $ *}

{if $feature_faqs eq 'y'}
    {if $nonums eq 'y'}
    {eval var="{tr}Top `$module_rows` Visited FAQs{/tr}" assign="tpl_module_title"}
    {else}
    {eval var="{tr}Top Visited FAQs{/tr}" assign="tpl_module_title"}
    {/if}

    {tikimodule title=$tpl_module_title name="top_visited_faqs" flip=$module_params.flip decorations=$module_params.decorations}
    <table  border="0" cellpadding="0" cellspacing="0">
    {section name=ix loop=$modTopVisitedFaqs}
	<tr>{if $nonums != 'y'}<td class="module" valign="top">{$smarty.section.ix.index_next})</td>{/if}
        <td class="module"><a class="linkmodule" href="tiki-view_faq.php?faqId={$modTopVisitedFaqs[ix].faqId}">{$modTopVisitedFaqs[ix].title}</a></td></tr>
    {/section}
    </table>
    {/tikimodule}
{/if}
