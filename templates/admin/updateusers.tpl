{ajaxheader modname=$modinfo.name filename='ldapauth.js' ui=true}
{strip}
{insert name='csrftoken' assign='csrftoken'}
{pageaddvarblock}
<script type="text/javascript">
	var users = {{$users2update}};
	var len = users.length;
	var i = 0;
	
	document.observe("dom:loaded", function() {
		Zikula.UI.Tooltips($$('.tooltips'));
		updateADUser();
	});
</script>
{/pageaddvarblock}
{/strip}

{adminheader}
<div class="z-admin-content-pagetitle">
	{icon type="view" size="small"}
	<h3>{gt text="Updating users"}</h3>
</div>

<div align="center" style="width: 100%;">
    <div class="ldapauth-progress-bar ldapauth-green ldapauth-shine" align="left">
        <span style="width:0%;" id="progbar">
        </span>
    </div>
</div>
<div align="center" id="percentage">0 %</div>


<div align="center" id="okbuttonarea" style="margin-top: 30px;" class="z-hide">
    <div class="z-buttonrow z-buttons z-center">
        <a href="{modurl modname='LDAPAuth' type='admin' func='userUpdate'}">{img modname='core' src='button_ok.png' set='icons/extrasmall' __alt='OK' __title='OK'} {gt text='OK'}</a>
    </div>
</div>

{adminfooter}