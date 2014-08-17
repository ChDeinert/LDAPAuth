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
    <h3>{gt text="LDAPAuth Profile Mapping"}</h3>
</div>

<div class="z-clearfix">
    <ul class="z-menulinks">
        <li>
            <a class="z-iconlink z-icon-es-new" href="{modurl modname='LDAPAuth' type='admin' func='addProfileMapping'}">
                {gt text='Add Mapping'}
            </a>
        </li>
    </ul>
</div>

<div class="z-form">
    <fieldset>
        <legend>{gt text='Available mappings'}</legend>
        
        <table class="z-datatable">
            <thead>
                <tr>
                    <th>{gt text='ID'}</th>
                    <th>{gt text='Mapping active'}</th>
                    <th>{gt text='Profile property ID'}</th>
                    <th>{gt text='LDAP attribute'}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$items item='item'}
                    <tr class="{cycle values='z-odd,z-even'}">
                        <td>{$item.id}</td>
                        <td align="center">
                            {if ($item.active == 1)}
                                {img modname='LDAPAuth' src='icons/16x16/checkbox-2.png'}
                            {else}
                                {img modname='LDAPAuth' src='icons/16x16/checkbox-1.png'}
                            {/if}
                        </td>
                        <td>{$item.prop_attribute_name}</td>
                        <td>{$attributes[$item.attribute]}</td>
                        <td>
                            <a class="z-iconlink z-icon-es-edit" href="{modurl modname='LDAPAuth' type='admin' func='editProfileMapping' id=$item.id}"></a>
                            <a class="z-iconlink z-icon-es-delete" href="{modurl modname='LDAPAuth' type='admin' func='deleteProfileMapping' id=$item.id}"></a>
                        </td>
                    </tr>
                {foreachelse}
                    <tr class="z-datatableempty">
                        <td colspan="5">{gt text='No Mappings available'}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </fieldset>
</div>
{adminfooter}