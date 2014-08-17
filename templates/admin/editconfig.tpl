{ajaxheader modname=$modinfo.name filename='ldapauth.js' ui=true}
{strip}
{insert name='csrftoken' assign='csrftoken'}
{pageaddvarblock}
<script type="text/javascript">
    document.observe("dom:loaded", function() {
        Zikula.UI.Tooltips($$('.tooltips'));
    });
</script>
{/pageaddvarblock}
{/strip}

{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="edit" size="small"}
    <h3>{gt text="Edit LDAPAuth Configuration with ID %s" tag1=$configurationdata.id}</h3>
</div>

{if ($update && $configUpdated)}
	<p class="z-statusmsg">
		{gt text='Configuration Updated'}
	</p>
{elseif ($update && !$configUpdated)}
	<p class="z-errormsg">
		{gt text='Could not update Configuration'}
	</p>
{elseif ($update && !$dataOK)}
	<p class="z-errormsg">
		{gt text='Error in input-data'}
	</p>
{/if}

<form class="z-form" id="newConfigForm" action="{modurl modname='LDAPAuth' type='admin' func='storeConfig'}" method="post" enctype="application/x-www-form-urlencoded">
	<div>
		<input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
		<input type="hidden" name="id" value="{$configurationdata.id}"/>
		<fieldset>
			<legend>{gt text='Configurations'}</legend>

			<div class="z-formrow">
				<label for="active">{gt text='Active Configuration'}</label>
				{if ($modulevars.active == 1)}
					<input type="checkbox" id="active" name="active" checked="checked" value="1"/>
				{else}
					<input type="checkbox" id="active" name="active" value="1"/>
				{/if}
			</div>

            <div class="z-formrow">
                <label for="profile">{gt text='Support Profile module'}</label>
                {if ($modulevars.profile == 1)}
                    <input type="checkbox" id="profile" name="profile" checked="checked" value="1"/>
                {else}
                    <input type="checkbox" id="profile" name="profile" value="1"/>
                {/if}
            </div>

			<div class="z-formrow">
				<label for="account_suffix">{gt text='Account Suffix'}</label>
				<input type="text" id="account_suffix" name="account_suffix" value="{$modulevars.account_suffix}"/>
			</div>

			<div class="z-formrow">
				<label for="base_dn">{gt text='Base DN'}</label>
				<input type="text" id="base_dn" name="base_dn" value="{$modulevars.base_dn}"/>
			</div>

			<div class="z-formrow">
				<label for="domain_controllers">{gt text='Domain Controllers'} {gt text='(separated with , )'}</label>
				<input type="text" id="domain_controllers" name="domain_controllers" value="{$modulevars.domain_controllers}"/>
			</div>

			<div class="z-formrow">
				<label for="admin_username">{gt text='Username'}</label>
				<input type="text" id="admin_username" name="admin_username" value="{$modulevars.admin_username}"/>
			</div>

			<div class="z-formrow">
				<label for="admin_password">{gt text='Password'}</label>
				<input type="password" id="admin_password" name="admin_password" value="{$modulevars.admin_password}"/>
			</div>

			<div class="z-formrow">
				<label for="real_primarygroup">{gt text='Real Primarygroup'}</label>
				{if ($modulevars.real_primarygroup == 1)}
					<input type="checkbox" id="real_primarygroup" name="real_primarygroup" checked="checked" value="1"/>
				{else}
					<input type="checkbox" id="real_primarygroup" name="real_primarygroup" value="1"/>
				{/if}
			</div>

			<div class="z-formrow">
				<label for="use_ssl">{gt text='Use SSL'}</label>
				{if ($modulevars.use_ssl == 1)}
					<input type="checkbox" id="use_ssl" name="use_ssl" checked="checked" value="1"/>
				{else}
					<input type="checkbox" id="use_ssl" name="use_ssl" value="1"/>
				{/if}
			</div>

			<div class="z-formrow">
				<label for="use_tsl">{gt text='Use TSL'}</label>
				{if ($modulevars.use_tsl == 1)}
					<input type="checkbox" id="use_tsl" name="use_tsl" checked="checked" value="1"/>
				{else}
					<input type="checkbox" id="use_tsl" name="use_tsl" value="1"/>
				{/if}
			</div>

			<div class="z-formrow">
				<label for="recursive_groups">{gt text='Recursive groups'}</label>
				{if ($modulevars.recursive_groups == 1)}
					<input type="checkbox" id="recursive_groups" name="recursive_groups" checked="checked" value="1"/>
				{else}
					<input type="checkbox" id="recursive_groups" name="recursive_groups" value="1"/>
				{/if}
			</div>

			<div class="z-formrow">
				<label for="ad_port">{gt text='AD Port'}</label>
				<input type="text" id="ad_port" name="ad_port" value="{$modulevars.ad_port}"/>
			</div>

			<div class="z-formrow">
				<label for="sso">{gt text='SSO'}</label>
				{if ($modulevars.sso == 1)}
					<input type="checkbox" id="sso" name="sso" checked="checked" value="1"/>
				{else}
					<input type="checkbox" id="sso" name="sso" value="1"/>
				{/if}
			</div>
		</fieldset>

		<div class="z-buttonrow z-buttons z-center">
			{button id=newConfigForm|cat:'_submit' type='submit' src='button_ok.png' set='icons/extrasmall' __alt='Save' __title='Save' __text='Save'}
			<a href="{modurl modname='LDAPAuth' type='admin' func='view'}">{img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
		</div>
	</div>
</form>

{adminfooter}
