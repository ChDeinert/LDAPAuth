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
    {icon type="view" size="small"}
    <h3>{gt text="LDAPAuth Configuration"}</h3>
</div>

<fieldset class="z-form">
	<legend>{gt text='Configuration'}</legend>
	
	<div class="z-formrow">
		<label for="active">{gt text='Active Configuration'}</label>
		{if ($modulevars.active == 1)}
			<input type="checkbox" id="active" name="active" disabled="disabled" checked="checked"/>
		{else}
			<input type="checkbox" id="active" name="active" disabled="disabled"/>
		{/if}
	</div>
	
    <div class="z-formrow">
        <label for="profile">{gt text='Support Profile module'}</label>
        {if ($modulevars.profile == 1)}
            <input type="checkbox" id="profile" name="profile" disabled="disabled" checked="checked"/>
        {else}
            <input type="checkbox" id="profile" name="profile" disabled="disabled"/>
        {/if}
    </div>
    
	<div class="z-formrow">
		<label for="account_suffix">{gt text='Account Suffix'}</label>
		<input type="text" id="account_suffix" name="account_suffix" disabled="disabled" value="{$modulevars.account_suffix}"/>
	</div>
	
	<div class="z-formrow">
		<label for="base_dn">{gt text='Base DN'}</label>
		<input type="text" id="base_dn" name="base_dn" disabled="disabled" value="{$modulevars.base_dn}"/>
	</div>
	
	<div class="z-formrow">
		<label for="domain_controllers">{gt text='Domain Controllers'} {gt text='(separated with , )'}</label>
		<input type="text" id="domain_controllers" name="domain_controllers" disabled="disabled" value="{$modulevars.domain_controllers}"/>
	</div>
	
	<div class="z-formrow">
		<label for="admin_username">{gt text='Username'}</label>
		<input type="text" id="admin_username" name="admin_username" disabled="disabled" value="{$modulevars.admin_username}"/>
	</div>
	
	<div class="z-formrow">
		<label for="admin_password">{gt text='Password'}</label>
		<input type="password" id="admin_password" name="admin_password" disabled="disabled" value="{$modulevars.admin_password}"/>
	</div>
	
	<div class="z-formrow">
		<label for="real_primarygroup">{gt text='Real Primarygroup'}</label>
		{if ($modulevars.real_primarygroup == 1)}
			<input type="checkbox" id="real_primarygroup" name="real_primarygroup" disabled="disabled" checked="checked"/>
		{else}
			<input type="checkbox" id="real_primarygroup" name="real_primarygroup" disabled="disabled"/>
		{/if}
	</div>
	
	<div class="z-formrow">
		<label for="use_ssl">{gt text='Use SSL'}</label>
		{if ($modulevars.use_ssl == 1)}
			<input type="checkbox" id="use_ssl" name="use_ssl" disabled="disabled" checked="checked"/>
		{else}
			<input type="checkbox" id="use_ssl" name="use_ssl" disabled="disabled"/>
		{/if}
	</div>
	
	<div class="z-formrow">
		<label for="use_tsl">{gt text='Use TSL'}</label>
		{if ($modulevars.use_tsl == 1)}
			<input type="checkbox" id="use_tsl" name="use_tsl" disabled="disabled" checked="checked"/>
		{else}
			<input type="checkbox" id="use_tsl" name="use_tsl" disabled="disabled"/>
		{/if}
	</div>
	
	<div class="z-formrow">
		<label for="recursive_groups">{gt text='Recursive groups'}</label>
		{if ($modulevars.recursive_groups == 1)}
			<input type="checkbox" id="recursive_groups" name="recursive_groups" disabled="disabled" checked="checked"/>
		{else}
			<input type="checkbox" id="recursive_groups" name="recursive_groups" disabled="disabled"/>
		{/if}
	</div>
	
	<div class="z-formrow">
		<label for="ad_port">{gt text='AD Port'}</label>
		<input type="text" id="ad_port" name="ad_port" disabled="disabled" value="{$modulevars.ad_port}"/>
	</div>
	
	<div class="z-formrow">
		<label for="sso">{gt text='SSO'}</label>
		{if ($modulevars.sso == 1)}
			<input type="checkbox" id="sso" name="sso" disabled="disabled" checked="checked"/>
		{else}
			<input type="checkbox" id="sso" name="sso" disabled="disabled"/>
		{/if}
	</div>
	
	<div class="z-buttonrow z-buttons z-center">
		<a href="{modurl modname='LDAPAuth' type='admin' func='editConfig'}">{img modname='core' src='configure.png' set='icons/extrasmall' __alt='Edit Configuration' __title='Edit Configuration'} {gt text='Edit Configuration'}</a>
	</div>
	
</fieldset>

{adminfooter}