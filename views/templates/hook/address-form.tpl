<div id="js-address-on-registration">
    {block name='form_fields'}
        {foreach from=$formFields item="field"}
            {if in_array($field.name, ['firstname','lastname','company']) }
                {continue}
            {/if}

            {block name='form_field'}
                {form_field field=$field}
            {/block}
        {/foreach}
    {/block}
</div>
