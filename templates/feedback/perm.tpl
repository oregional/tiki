{* $Id$ *}
{if $fb|count > 0}
	{foreach $fb as $key => $info}
		{if $info.count|count > 0}
			{if $info.count|count == 1}
				{$poptitle = "{tr}Permission successfully changed{/tr}"}
				{if $info.type === 'global'}
					{$header = "{tr}Global permission{/tr}"}
				{else}
					{if $info.objid !== $info.objname}
						{$header = "{tr _0=$info.objtype _1="<em>{$info.objname}</em>" _2=$info.objid}Object permission for the %0 %1 (ID %2){/tr}"}
					{else}
						{$header = "{tr _0=$info.objtype _1="<em>{$info.objname}</em>"}Object permission for the %0 %1{/tr}"}
					{/if}
				{/if}
			{else}
				{$poptitle = "{tr}Permissions successfully changed{/tr}"}
				{if $info.type === 'global'}
					{$header = "{tr}Global permissions{/tr}"}
				{else}
					{if $info.objid !== $info.objname}
						{$header = "{tr _0=$info.objtype _1="<em>{$info.objname}</em>" _2=$info.objid}Object permissions for the %0 %1 (ID %2){/tr}"}
					{else}
						{$header = "{tr _0=$info.objtype _1="<em>{$info.objname}</em>"}Object permissions for the %0 %1{/tr}"}
					{/if}
				{/if}
			{/if}
			{remarksbox type='feedback' title="$poptitle"}
				<h5>{$header}:</h5>
				{foreach $info.mes as $direction => $change}
					{if !empty($direction)}
						{$direction|capitalize}
						<ul>
							{foreach $change as $group => $perm}
								{if count($perm) > 0}
									{foreach $perm as $name}
										<li>
											{$group}: {$name}
										</li>
									{/foreach}
								{/if}
							{/foreach}
						</ul>
					{/if}
				 {/foreach}
			{/remarksbox}
		{else}
			{remarksbox type='note' title="{tr}No permissions were changed{/tr}"}{/remarksbox}
		{/if}
	{/foreach}
{/if}
