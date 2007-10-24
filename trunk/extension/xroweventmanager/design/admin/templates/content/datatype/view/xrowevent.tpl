{def $person_array=$attribute.content.persons
     $start_date=$attribute.content.start_date
     $max_participants=$attribute.content.max_participants
}
{if $start_date|gt(0)}
    <div class="block">
        <div class="element">
            <strong>{"Start"|i18n("extension/xroweventmanager")}:</strong><br />
            {$start_date|l10n(datetime)}
        </div>
    </div>    
    <div class="break"></div>
    <div class="block">
        <div class="element">
            <strong>{"End"|i18n("extension/xroweventmanager")}:</strong><br />
            {$attribute.content.end_date|l10n(datetime)}
        </div>
    </div>    
    <div class="break"></div>
    {if $max_participants|gt(0)}
    <div class="block">    
        <div class="element">
            <strong>{"Max participants"|i18n("extension/xroweventmanager")}:</strong><br />
            {$max_participants}
        </div>
    </div>    
    <div class="break"></div>
    {/if}
    <div class="block">    
        <div class="element">
            <strong>{"Status"|i18n("extension/xroweventmanager")}:</strong><br />
            {$attribute.content.status_text|wash}
        </div>
    </div>
    <div class="break"></div>
    <div class="block">
        <div class="element">
            <strong>{$person_array|count} {"person(s)"|i18n("extension/xroweventmanager")}</strong><br />
            {if $person_array|count|gt(0)}
                {foreach $person_array as $key => $person }
                    {$person.user_object.name|wash}<br/>
                {/foreach}
            {else}
                <p>{"No persons added yet."|i18n("extension/xroweventmanager")}</p>
            {/if}
        </div>
    </div>
    {*
    <div class="break"></div>
    {if or($attribute.content.person_user_exists,$admin)}
    {def $admin=fetch( 'user', 'has_access_to', hash( 'module', 'xrowevent', 'function', 'admin' ) )
         $participants_array=$attribute.content.participants
         $participants_count=$participants_array|count}
    <div class="block">
        <div class="element">
            <strong>{$participants_count} {"participant(s)"|i18n("extension/xroweventmanager")}</strong><br />
            {if $participants_count|gt(0)}
                <ul>
                {foreach $participants_array as $key => $participant }
                    <li>{$participant.user_object.name|wash}</li>
                {/foreach}
                </ul>
            {else}
                <p>{"No participant has joined this event yet."|i18n("extension/xroweventmanager")}</p>
            {/if}
        </div>
    </div>
    <div class="break"></div>
    {/if}
    *}
{/if}