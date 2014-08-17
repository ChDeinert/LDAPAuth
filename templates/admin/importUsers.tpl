{ajaxheader modname=$modinfo.name filename='ldapauth.js' ui=true}
{strip}
{insert name='csrftoken' assign='csrftoken'}
{pageaddvarblock}
<script type="text/javascript"> 
var users = {{$items}};
var len = users.length;
var i = 0;

document.observe("dom:loaded", function() {
    Zikula.UI.Tooltips($$('.tooltips'));
    processUserImports();
});
</script>
{/pageaddvarblock}
{/strip}

{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="view" size="small"}
    <h3>{gt text="Import AD Users"}</h3>
</div>

<div align="center"><b>{gt text='Current User:'}</b> <span id="currentuser">&nbsp;</span></div>
<div align="center" style="width: 100%;">
    <div class="ldapauth-progress-bar ldapauth-green ldapauth-shine" align="left">
        <span style="width:0%;" id="progbar">
        </span>
    </div>
</div>
<div align="center" id="percentage">0 %</div>

<div align="center" id="okbuttonarea" style="margin-top: 30px;" class="z-hide">
    <div class="z-buttonrow z-buttons z-center">
        <a href="{modurl modname='LDAPAuth' type='admin' func='userImport'}" class="z-btgreen z-bt-ok">{gt text='OK'}</a>
    </div>
</div>

{adminfooter}
