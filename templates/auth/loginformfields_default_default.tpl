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
    <label for="users_login_login_id">{strip}
        {gt text='User name'}
    {/strip}</label>
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