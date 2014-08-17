<fieldset>
    <legend>{gt text='Mapping'}</legend>
    
    <div class="z-formrow">
        <label for="active">{gt text='Active'}</label>
        <input type="checkbox" name="active" id="active" value="1" {if ($item.active==1)}checked="checked"{/if}/>
    </div>
    <div class="z-formrow">
        <label for="prop_id">{gt text='Property in Profile'}</label>
        <select name="prop_id" id="prop_id">
            {foreach from=$properties item=property}
                <option value="{$property.prop_id}" {if ($item.prop_id == $property.prop_id)}selected="selected"{/if}>
                    {$property.prop_attribute_name}
                </option>
            {/foreach}
        </select>
    </div>
    <div class="z-formrow">
        <label for="attribute">{gt text='LDAP Attibute'}</label>
        <select name="attribute" id="attribute">
            {foreach from=$attributes item='attribute' key='attcode'}
                <option value="{$attcode}" {if ($item.attribute == $attcode)}selected="selected"{/if}>
                    {$attribute}
                </option>
            {/foreach}
        </select>
    </div>
</fieldset>
