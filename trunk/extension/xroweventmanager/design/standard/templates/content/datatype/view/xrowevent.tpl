{def $person_array=$attribute.content.persons
     $person_count=$person_array|count
     $participants_array=$attribute.content.participants
     $participants_count=$participants_array|count
     $current_user=fetch( 'user', 'current_user' )
     $max_participants=$attribute.content.max_participants
     $start_date=$attribute.content.start_date
     $date=currentdate()
     $admin=fetch( 'user', 'has_access_to', hash( 'module', 'xrowevent', 
                                                  'function', 'admin' ) )}
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
    <div class="block">    
        <div class="element">
            <strong>{"Max participants"|i18n("extension/xroweventmanager")}:</strong><br />
            {$max_participants|wash}
        </div>
    </div>    
    <div class="break"></div>
    <div class="block">    
        <div class="element">
            <strong>{"Status"|i18n("extension/xroweventmanager")}:</strong><br />
            {$attribute.content.status_text|wash}
        </div>
    </div>
    <div class="break"></div>
    <div class="block">
        <div class="element">
            <strong>{$person_count|wash} {"person(s)"|i18n("extension/xroweventmanager")}</strong><br />
            {if $person_count|gt(0)}
                {foreach $person_array as $key => $person }
                    {$person.user_object.name|wash}<br/>
                {/foreach}
            {else}
                <p>{"No persons added yet."|i18n("extension/xroweventmanager")}</p>
            {/if}
        </div>
    </div>
    <div class="break"></div>
    {if $start_date|gt($date)}
        {if $current_user.is_logged_in}
            <form action={"/xrowevent/action"|ezurl} method="post" name="xroweventform" id="xroweventform">
                <input type="hidden" name="EventID" value="{$attribute.content.contentobject_id}" />
            {if $participants_count|lt($max_participants)}
                {if $attribute.content.participant_user_exists|not}
                    <p>{"You are able to join this event."|i18n("extension/xroweventmanager")}</p>
                    <input class="button" type="submit" name="AddParticipant" value="{'Join event'|i18n( 'extension/xroweventmanager' )}" />
                {else}
                    <p>{"You have already joined this event."|i18n("extension/xroweventmanager")}</p>
                    <input class="button" type="submit" name="RemoveParticipant" value="{'Cancel participation'|i18n( 'extension/xroweventmanager' )}" />
                {/if}
            {else}
                <p>{"Maximum number of participants reached."|i18n("extension/xroweventmanager")}</p>
            {/if}
            </form>
        {else}
            <p>{"If you want to join this event, please login:"|i18n("extension/xroweventmanager")}
            <a href={"user/login"|ezurl}>{"Login"|i18n("extension/xroweventmanager")}</a></p>
        {/if}
    {else}
        <p>{"Event is in the past, subscription isn't possible."|i18n("extension/xroweventmanager")}</p>
    {/if}
    
    {* Show all participants to all event persons *}
    {if or($attribute.content.person_user_exists,$admin)}
        <div class="block">
            <div class="element">
                <strong>{$participants_count} {"participant(s)"|i18n("extension/xroweventmanager")}</strong><br />
                {if $participants_count|gt(0)}
                    <ul>
                    {foreach $participants_array as $key => $participant }
                        <li>{$participant.user_object.name|wash}</li>
                    {/foreach}
                    </li>
                {else}
                    <p>{"No participant has joined this event yet."|i18n("extension/xroweventmanager")}</p>
                {/if}
            </div>
        </div>
        <div class="break"></div>
    {/if}
{/if}