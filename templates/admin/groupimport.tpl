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
	<h3>{gt text="AD Groups to import"}</h3>
</div>
<fieldset>
	<legend>{gt text='AD Groups'}</legend>
	<form class="z-form" id="importADGroupsForm" name="importADGroupsForm" action="{modurl modname='LDAPAuth' type='admin' func='importGroups'}" method="post" enctype="application/x-www-form-urlencoded">
		<input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
		<table class="z-datatable">
			<thead>
				<tr>
					<th>
						
					</th>
					<th>
						{gt text='Group name'}
					</th>
					<th>
						{gt text='Group description'}
					</th>
				</tr>
			</thead>
			<tbody class="z-clearer">
				{section name='newadgroups' loop=$newadgroups}
					<tr class="{cycle values='z-odd,z-even'}">
						<td>
							<input type="checkbox" id="groups" name="groups[]" value="{$newadgroups[newadgroups].name}"/>
						</td>
						<td>{$newadgroups[newadgroups].name}</td>
						<td>{$newadgroups[newadgroups].desc}</td>
					</tr>
				{/section}
			</tbody>
		</table>
		<div class="z-buttonrow z-buttons z-center">
			<a href="javascript:void(0)" onclick="checkAll(document.importADGroupsForm.groups)">{img modname='LDAPAuth' src='icons/16x16/checkbox-2.png' __alt='Check all' __title='Check all'} {gt text='Check all'}</a>
			<a href="javascript:void(0)" onclick="uncheckAll(document.importADGroupsForm.groups)">{img modname='LDAPAuth' src='icons/16x16/checkbox-1.png' __alt='Uncheck all' __title='Uncheck all'} {gt text='Uncheck all'}</a>
		</div>
		<div class="z-buttonrow z-buttons z-center">
			{button id=importADGroupsForm|cat:'_submit' type='submit' src='button_ok.png' set='icons/extrasmall' __alt='import' __title='import' __text='import'}
			<a href="{modurl modname='LDAPAuth' type='admin' func='view'}">{img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}</a>
		</div>
	</form>
</fieldset>

{adminfooter}