{if empty($user)}
	{$headerlib->add_jsfile('lib/captcha/captchalib.js')}
	{if $antibot_table ne 'y'}
	<tr{if !empty($tr_style)} class="{$tr_style}"{/if}>
		<td{if !empty($td_style)} class="{$td_style}"{/if}>
	{else}
		<div class="antibot1">
	{/if}
			{tr}Anti-Bot verification code{/tr}:<br />
			{if $captchalib->type eq 'default'}
				<a id="captchaRegenerate">{tr}(regenerate anti-bot code){/tr}</a>
			{/if}
	{if $antibot_table ne 'y'}
		</td>
		<td id="captcha" {if !empty($td_style)} class="{$td_style}"{/if}>
	{else}
		</div>
		<div class="antibot2">
	{/if}
			{if $captchalib->type eq 'recaptcha'}
				{$captchalib->render()}
			{else}
				{$captchalib->generate()}
				<input type="hidden" name="captcha[id]" id="captchaId" value="{$captchalib->getId()}">
				{if $captchalib->type eq 'default'}
					<img id="captchaImg" src="{$captchalib->getPath()}" alt="{tr}Anti-Bot verification code image{/tr}" />
				{else}
					{* dumb captcha *}
					{$captchalib->render()}
				{/if}
			{/if}
	{if $antibot_table ne 'y'}
		</td>
	</tr>
	{else}
		</div>
	{/if}
	{if $captchalib->type ne 'recaptcha'}
		{if $antibot_table ne 'y'}
		<tr{if !empty($tr_style)} class="{$tr_style}"{/if}>
			<td{if !empty($td_style)} class="{$td_style}"{/if}>
		{else}
			<div class="antibot3">
		{/if}
				<label for="antibotcode">{tr}Enter the code you see above{/tr}{if $showmandatory eq 'y'}*{/if}:</label>
		{if $antibot_table ne 'y'}
			</td>
			<td{if !empty($td_style)} class="{$td_style}"{/if}>
		{else}
			</div>
			<div class="antibot4">
		{/if}
				<input type="text" maxlength="8" size="8" name="captcha[input]" id="antibotcode" />
		{if $antibot_table ne 'y'}
			</td>
		</tr>
		{else}
			</div>
		{/if}
	{/if}
{/if}
