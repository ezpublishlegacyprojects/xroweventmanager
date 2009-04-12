{* we shouldn`t cache this *}
{set-block scope=root variable=cache_ttl}0{/set-block}
{def $person_array=$attribute.content.persons
     $person_count=$person_array|count
     $participants_count=$attribute.content.participants_count
     $current_user=fetch( 'user', 'current_user' )
     $max_participants=$attribute.content.max_participants
     $start_date=$attribute.content.start_date
     $date=currentdate()
     $admin=fetch( 'user', 'has_access_to', hash( 'module', 'xrowevent', 
                                                  'function', 'admin' ) )}
{if $start_date|gt(0)}
<div class="hide">
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
            {$max_participants|wash}
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
</div>
    <div class="break"></div>
    {* This functionality should be only accessable over the backend's event tab.
       We leave it in here if one might still need it.
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
    *}
    <div class="break"></div>
    {if $start_date|gt($date)}
        {if $current_user.is_logged_in}
            <form action={"/xrowevent/action"|ezurl} method="post" name="xroweventform" id="xroweventform">
                <input type="hidden" name="EventID" value="{$attribute.content.contentobject_id}" />
            {if $max_participants|gt(0)}
                {if $participants_count|lt($max_participants)}
                    {if $attribute.content.participant_user_exists|not}
                        <p>{"You are able to join this event."|i18n("extension/xroweventmanager")}</p>
                        <input class="button" type="submit" name="AddParticipant" value="{'Join event'|i18n( 'extension/xroweventmanager' )}" />
                    {else}
                        <p>{"You have joined this event."|i18n("extension/xroweventmanager")}</p>
                        <input class="button" type="submit" name="RemoveParticipant" value="{'Cancel participation'|i18n( 'extension/xroweventmanager' )}" />
                    {/if}
                {else}
                    {if $attribute.content.participant_user_exists}
                    <p>{"You have joined this event."|i18n("extension/xroweventmanager")}</p>
                        <input class="button" type="submit" name="RemoveParticipant" value="{'Cancel participation'|i18n( 'extension/xroweventmanager' )}" />
                    {else}
                        <p>{"Maximum number of participants reached."|i18n("extension/xroweventmanager")}</p>
                    {/if}
                {/if}
            {else}
                {if $attribute.content.participant_user_exists|not}
                    <p>{"You are able to join this event."|i18n("extension/xroweventmanager")}</p>
                    <input class="button" type="submit" name="AddParticipant" value="{'Join event'|i18n( 'extension/xroweventmanager' )}" />
                {else}
                    <p>{"You have joined this event."|i18n("extension/xroweventmanager")}</p>
                    <input class="button" type="submit" name="RemoveParticipant" value="{'Cancel participation'|i18n( 'extension/xroweventmanager' )}" />
                {/if}
            {/if}
            </form>
        {else}
            <p>{"If you want to join this event, please login:"|i18n("extension/xroweventmanager")}</p>
            <form action={"user/login"|ezurl} method="post" enctype="multipart/form-data">
            <div class="block">
                 <div class="element">
                    <label for="id1">{"Username"|i18n("design/ezwebin/user/login",'User name')}
                    <input class="halfbox" type="text" size="10" name="Login" id="id1" value="" tabindex="1" />
                    </label>
                 </div>
                 <div class="element">
                    <label for="id2">{"Password"|i18n("design/ezwebin/user/login")}
                    <input class="halfbox" type="password" size="10" name="Password" id="id2" value="" tabindex="1" />
                    </label>
                 </div>
                 <div class="element">
                    <input class="defaultbutton" type="submit" name="LoginButton" value="{'Login'|i18n('design/ezwebin/user/login','Button')}" tabindex="1" />
                 </div>
                 <div class="element">
                    <input class="button" type="submit" name="RegisterButton" id="RegisterButton" value="{'Register new user'|i18n('design/ezwebin/user/login','Button')}" tabindex="1" />
                 </div>
            </div>
            <input name="RedirectURI" type="hidden" value="{$attribute.object.main_node.url_alias}" /> 
            </form>  
           <p><a class="linkRed" target="_blank" title={"Event Registration Help"|i18n("extension/xroweventmanager")} href="http://www.epsiplus.net/layout/set/helppopup/media/site_help/event_registration_help_text" onClick="return popup(this, 'Registration Help')">{"More help..."|i18n("extension/xroweventmanager")}</a>
           </p>     
        {/if}
    {else}
        <p>{"Event subscription has ended. A subscription isn't possible anymore."|i18n("extension/xroweventmanager")}</p>
    {/if}
    
    {* Show all participants to all event persons *}
    {*
    {if or($attribute.content.person_user_exists,$admin)}
    {def $participants_array=$attribute.content.participants}
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
    *}
{/if}
{literal}
<SCRIPT TYPE="text/javascript">
<!--
function popup(mylink, windowname)
{
if (! window.focus)return true;
var href;
if (typeof(mylink) == 'string')
   href=mylink;
else
   href=mylink.href;
window.open(href, windowname, 'width=500,height=600,scrollbars=yes');
return false;
}
//-->
</SCRIPT>
{/literal}