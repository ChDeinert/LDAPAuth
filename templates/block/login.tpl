{strip}
{ajaxheader modname='LDAPAuth' filename='Zikula.LDAPAuth.LoginBlock.js'}
{/strip}

<div class="users_loginblock_box">
	<div id="users_loginblock_waiting" class="z-center z-hide">
		{img modname='core' set='ajax' src='indicator_circle.gif'}
	</div>
	<form id="users_loginblock_login_form" class="z-form z-linear" action="{modurl modname='Users' type='user' func='login'}" method="post">
		<div>
			<input type="hidden" id="users_loginblock_returnpage" name="returnpage" value="{$returnpage}" />
			<input type="hidden" id="users_loginblock_csrftoken" name="csrftoken" value="{insert name='csrftoken'}" />
			<input id="users_login_event_type" type="hidden" name="event_type" value="login_block" />
			<input type="hidden" id="users_loginblock_selected_authentication_module" name="authentication_method[modname]" value="{if isset($selected_authentication_method) && $selected_authentication_method}{$selected_authentication_method.modname|default:'false'}{/if}" />
			<input type="hidden" id="users_loginblock_selected_authentication_method" name="authentication_method[method]" value="{if isset($selected_authentication_method) && $selected_authentication_method}{$selected_authentication_method.method|default:'false'}{/if}" />
			{if ($modvars.ZConfig.seclevel|lower == 'high')}
				<input id="users_loginblock_rememberme" type="hidden" name="rememberme" value="0" />
			{/if}
			
			<div id="users_loginblock_fields">
				{strip}
					{gt text="User account" assign='legend_text'}
					{if isset($change_password) && ($change_password == 1) && ($modvars.Users.use_password_strength_meter == 1)}
						{pageaddvar name='javascript' value='prototype'}
						{pageaddvar name='javascript' value='system/Users/javascript/Zikula.Users.PassMeter.js'}
						{pageaddvarblock}
							<script type="text/javascript">
								var passmeter = null;
								document.observe("dom:loaded", function() {
									passmeter = new Zikula.Users.PassMeter('users_login_newpass', 'users_login_passmeter', {
										username:'users_login_login_id',
										minLength: '{{$modvars.Users.minpass}}'
									});
								});
							</script>
						{/pageaddvarblock}
					{/if}
				{/strip}
				
				<div class="z-formrow">
					<label for="users_login_login_id">
						{strip}
							{gt text='User name'}
						{/strip}
					</label>
					<input id="users_login_login_id" type="text" name="authentication_info[login_id]" maxlength="64" value="{if isset($authentication_info.login_id)}{$authentication_info.login_id}{/if}" />
				</div>
				
				<script type="text/javascript">
					function capLock(e) {
						kc = e.keyCode?e.keyCode:e.which;
						sk = e.shiftKey?e.shiftKey:((kc == 16)?true:false);
						if ((((kc >= 65 && kc <= 90) && !sk)||((kc >= 97 && kc <= 122) && sk)) && !Boolean(window.chrome) && !Boolean(window.webkit))
							document.getElementById('capsLok').style.visibility = 'visible';
						else
							document.getElementById('capsLok').style.visibility = 'hidden';
					}
				</script>
				
				<div class="z-formrow">
					<label for="users_login_pass">{if isset($change_password) && $change_password}{gt text='Current password'}{else}{gt text='Password'}{/if}</label>
					<input id="users_login_pass" type="password" name="authentication_info[pass]" maxlength="25" onkeypress="capLock(event)" />
					<em class="z-formnote z-sub" id="capsLok" style="visibility:hidden">{gt text='Caps Lock is on!'}</em>
				</div>
			</div>
			{if $modvars.ZConfig.seclevel|lower != 'high'}
				<div class="z-formrow z-clearer">
					<div>
						<input id="users_loginblock_rememberme" type="checkbox" name="rememberme" value="1" />
						<label for="users_loginblock_rememberme">{gt text="Keep me logged in on this computer"}</label>
					</div>
				</div>
				
				{notifyevent eventname='module.users.ui.form_edit.login_block' assign="eventData"}
				{foreach item='eventDisplay' from=$eventData}
					{$eventDisplay}
				{/foreach}
				
				{notifydisplayhooks eventname='users.ui_hooks.login_block.form_edit' id=null}
				
			{/if}
			<div class="z-buttons z-right">
				<input class="z-bt-ok z-bt-small" id="users_loginblock_submit" name="users_loginblock_submit" type="submit" value="{gt text='Log in'}" />
			</div>
		</div>
	</form>
</div>
