{def $page_limit=15
     $participant_array=fetch( 'xrowevent', 'participant_list', hash( 'event_id', $event_id,
                                                                      'offset', $offset,
                                                                      'limit', $page_limit,
                                                                      'sort_array', array( array( 'created', 'desc' ) ) ) )
    $max_participants=$event.max_participants
}

<form action={concat( 'xrowevent/action' )|ezurl} method="post" name="xrowevent" id="xrowevent">

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$event.event_object.name|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">
<div class="block">
    {if $event.event_object.data_map.description.content.is_empty|not}

        <div class="attribute-long">
            {attribute_view_gui attribute=$event.event_object.data_map.description}
        </div>
    {/if}
    <div class="element">
        <strong>{"Start"|i18n("extension/xroweventmanager")}:</strong><br />
        {$event.start_date|l10n(datetime)}
    </div>
</div>
<div class="break"></div>
<div class="block">
    <div class="element">
        <strong>{"End"|i18n("extension/xroweventmanager")}:</strong><br />
        {$event.end_date|l10n(datetime)}
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
        {$event.status_text|wash}
    </div>
</div>

<div class="block">
    <div class="element">
        <strong>{"Comments allowed"|i18n("extension/xroweventmanager")}:</strong><br />
        {if $event.comment|eq(1)}{"Yes"|i18n("extension/xroweventmanager")}{else}{"No"|i18n("extension/xroweventmanager")}{/if}
    </div>
</div>

{if $person_count|gt(0)}
<div class="break"></div>
<div class="block">

<h3>{$person_count} {"person(s)"|i18n( 'extension/xroweventmanager' )}</h3>
<table class="list" cellspacing="0">
<tr>
    <th class="wide">{'Person(s)'|i18n( 'extension/xroweventmanager' )}</th>
</tr>
{foreach $event.persons as $key => $person sequence array( bglight, bgdark ) as $seq}
<tr class="{$seq}">
    <td><a href={$person.user_object.main_node.url_alias|ezurl}>{$person.user_object.name|wash}</a></td>
</tr>
{/foreach}
</table>
</div>
{/if}

<div class="break"></div>
<div class="block">

{if $participants_count|gt(0)}
<h3>{$participants_count} {"participant(s)"|i18n( 'extension/xroweventmanager' )}</h3>

<div class="content-navigation-childlist">
    <table class="list" cellspacing="0">
    <tr>
        {* Remove column *}
        <th class="remove"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection.'|i18n( 'extension/xroweventmanager' )}" title="{'Invert selection.'|i18n( 'extension/xroweventmanager' )}" onclick="ezjs_toggleCheckboxes( document.xrowevent, 'DeleteIDArray[]' ); return false;" /></th>

        {* Name column *}
        <th>{'Name'|i18n( 'extension/xroweventmanager' )}</th>

        {* Class type column *}
        <th class="class">{'Joined'|i18n( 'extension/xroweventmanager' )}</th>
        <th class="name">{'Comment'|i18n( 'extension/xroweventmanager' )}</th>
    </tr>

    {foreach $participant_array as $key => $item sequence array( bglight, bgdark ) as $seq}

        <tr class="{$seq}">

        {* Remove checkbox *}
        <td>
            <input type="checkbox" name="DeleteIDArray[]" value="{$item.user_id}" title="{'Use these checkboxes to select items for removal. Click the "Remove selected" button to actually remove the selected items.'|i18n( 'extension/xroweventmanager' )|wash()}" />
        </td>

        <td style="white-space: nowrap;"><a href={$item.user_object.main_node.url_alias|ezurl}>{$item.user_object.name|wash}</a>
	</td>
	<td style="white-space: nowrap;">{if $item.created|gt(0)}{$item.created|l10n( shortdatetime )}{else}&nbsp;{/if}</td>
	<td><div style="width: 100%;height: 50px; overflow: scroll;">{$item.comment|wash}</div></td>
  </tr>
    {/foreach}

</table>

{else}
    <p>{"No participants yet."|i18n( 'extension/xroweventmanager' )}</p>
{/if}
</div>
<div class="block">

<input class="button" type="submit" name="RemoveParticipantListButton" value="{'Remove selected'|i18n( 'extension/xroweventmanager' )}" title="{'Remove the selected participants from the list above.'|i18n( 'extension/xroweventmanager' )}" />

{if $event.status|lt(3)}
<input class="button" type="submit" name="CancelEventButton" value="{'Cancel event'|i18n( 'extension/xroweventmanager' )}" title="{'Cancels the event.'|i18n( 'extension/xroweventmanager' )}" />
{else}
<input class="button" type="submit" name="ActivateEventButton" value="{'Activate event'|i18n( 'extension/xroweventmanager' )}" title="{'Activate the event.'|i18n( 'extension/xroweventmanager' )}" />
{/if}
{if $event.event_object.can_edit}
<input class="button" type="submit" name="EditEventButton" value="{'Edit event'|i18n( 'extension/xroweventmanager' )}" title="{'Edit the event.'|i18n( 'extension/xroweventmanager' )}" />
{/if}

<input class="button" type="submit" name="ExportEventButton" value="{'Export participants'|i18n( 'extension/xroweventmanager' )}" title="{'Export all participants of the event.'|i18n( 'extension/xroweventmanager' )}" />

<input type="hidden" name="EventID" value="{$event_id}" />
<input type="hidden" name="RedirectURIAfterPublish" value="{concat( "xrowevent/event/", $event_id )} />
<input type="hidden" name="RedirectURIAfterExport" value={concat( "xrowevent/event/", $event_id )} />

</div>

</div>

<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=concat('xrowevent/event/',$event_id)
         item_count=$participants_count
         view_parameters=$view_parameters
         item_limit=$page_limit}
</div>

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>

</form>
