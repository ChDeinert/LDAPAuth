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
    <h3>{gt text="Add profile mapping"}</h3>
</div>

<form class="z-form" id="storeProfileMapping" action="{modurl modname='LDAPAuth' type='admin' func='storeProfileMapping'}" method="post" enctype="application/x-www-form-urlencoded">
    <div>
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}"/>
        <input type="hidden" name="action" value="new"/>
        
        {include file='admin/part/form.profileMapping.tpl'}
        
        <div class="z-buttonrow z-buttons z-center">
            {button id=storeProfileMapping|cat:'_submit' type='submit' src='button_ok.png' set='icons/extrasmall' __alt='Save' __title='Save' __text='Save'} 
            <a href="{modurl modname='LDAPAuth' type='admin' func='viewProfileMapping'}">
                {img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel' __title='Cancel'} {gt text='Cancel'}
            </a>
        </div>
    </div>
</form>

{adminfooter}