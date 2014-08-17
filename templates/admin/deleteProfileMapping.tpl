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
    {icon type="new" size="small"}
    <h3>{gt text="Delete profile mapping with ID %s" tag1=$id}</h3>
</div>

<form class="z-form" id="deleteProfileMapping" action="{modurl modname='LDAPAuth' type='admin' func='deleteProfileMapping'}" method="post" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}"/>
        <input type="hidden" name="id" value="{$id}"/>
        
        <fieldset>
            <legend>{gt text='Mapping'}</legend>
            
            <div class="z-formrow">
                <label for="active">{gt text='Active'}</label>
                <input disabled="disabled" type="checkbox" name="active" id="active" value="1" {if ($item.active==1)}checked="checked"{/if}/>
            </div>
            <div class="z-formrow">
                <label for="prop_id">{gt text='Property in Profile'}</label>
                <select disabled="disabled" name="prop_id" id="prop_id">
                    {foreach from=$properties item=property}
                        <option value="{$property.prop_id}" {if ($item.prop_id == $property.prop_id)}selected="selected"{/if}>
                            {$property.prop_attribute_name}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="z-formrow">
                <label for="attribute">{gt text='LDAP Attibute'}</label>
                <select disabled="disabled" name="attribute" id="attribute">
                    {foreach from=$attributes item='attribute'}
                        <option {if ($item.attribute == $attribute)}selected="selected"{/if}>
                            {$attribute}
                        </option> 
                    {/foreach}
                </select>
            </div>
        </fieldset>
        
        <div class="z-buttonrow z-buttons z-center">
            {button id=deleteProfileMapping|cat:'_submit' type='submit' src='button_ok.png' set='icons/extrasmall' __alt='Delete' __title='Delete' __text='Delete'} 
            <a href="{modurl modname='LDAPAuth' type='admin' func='viewProfileMapping'}">
                {img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}
            </a>
        </div>
    </div>
</form>

{adminfooter}