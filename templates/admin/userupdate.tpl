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
	<h3>{gt text="AD Users to update"}</h3>
</div>
<form class="z-form" id="updateADUsers" name="updateADUsers" action="{modurl modname='LDAPAuth' type='admin' func='updateADUsers'}" method="post" enctype="application/x-www-form-urlencoded">
    <fieldset>
    	<legend>{gt text='AD Users'}</legend>
    		<input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
    		<table class="z-datatable">
    			<thead>
    				<tr>
    					<th>
    						
    					</th>
    					<th>
    						{gt text='User name'}
    					</th>
    					<th>
    						{gt text='Last update'}
    					</th>
                        <th>
                            {gt text='Last Change in AD'}
                        </th>
    				</tr>
    			</thead>
    			<tbody class="z-clearer">
    				{foreach from=$users2update item='user'}
    					<tr class="{cycle values='z-odd,z-even'}">
    						<td>
    							<input type="checkbox" id="users" name="users[]" value="{$user.uid}"/>
    						</td>
    						<td>{$user.uname}</td>
    						<td>{$user.lastchange}</td>
                            <td>{$user.whenchanged}</td>
    					</tr>
    				{foreachelse}
    					<tr class="z-datatableempty">
    						<td colspan="6">{gt text='No Users found.'}</td>
    					</tr>
    				{/foreach}
    			</tbody>
    		</table>
    		<div class="z-buttonrow z-buttons z-center">
    			<a href="javascript:void(0)" onclick="checkAll(document.updateADUsers.users)">{img modname='LDAPAuth' src='icons/16x16/checkbox-2.png' __alt='Check all' __title='Check all'} {gt text='Check all'}</a>
    			<a href="javascript:void(0)" onclick="uncheckAll(document.updateADUsers.users)">{img modname='LDAPAuth' src='icons/16x16/checkbox-1.png' __alt='Uncheck all' __title='Uncheck all'} {gt text='Uncheck all'}</a>
    		</div>
    		<div class="z-buttonrow z-buttons z-center">
    			{button id=updateADUsers|cat:'_submit' type='submit' src='button_ok.png' set='icons/extrasmall' __alt='Update' __title='Update' __text='Update'}
    			<a href="{modurl modname='LDAPAuth' type='admin' func='view'}">{img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
    		</div>
    </fieldset>
</form>

{adminfooter}