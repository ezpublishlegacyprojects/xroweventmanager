<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Events [%count]'|i18n( 'extension/xroweventmanager',, hash( '%count', $event_list|count ) )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $event_list|gt(0)}
<div class="context-toolbar">
<div class="block">
<div class="left">
<p>
{if eq( ezpreference( 'admin_eventlist_sortfield' ), 'event_name' )}
    <a href={'/user/preferences/set/admin_eventlist_sortfield/start_date/xrowevent/list/'|ezurl}>{'Start time'|i18n( 'extension/xroweventmanager' )}</a>
    <span class="current">{'Event name'|i18n( 'extension/xroweventmanager' )}</span>
{else}
    <span class="current">{'Start time'|i18n( 'extension/xroweventmanager' )}</span>
    <a href={'/user/preferences/set/admin_eventlist_sortfield/event_name/xrowevent/list/'|ezurl}>{'Event name'|i18n( 'extension/xroweventmanager' )}</a>
{/if}
</p>
</div>
<div class="right">
<p>
{if eq( ezpreference( 'admin_eventlist_sortorder' ), 'desc' )}
    <a href={'/user/preferences/set/admin_eventlist_sortorder/asc/xrowevent/list/'|ezurl}>{'Ascending'|i18n( 'extension/xroweventmanager' )}</a>
    <span class="current">{'Descending'|i18n( 'extension/xroweventmanager' )}</span>
{else}
    <span class="current">{'Ascending'|i18n( 'extension/xroweventmanager' )}</span>
    <a href={'/user/preferences/set/admin_eventlist_sortorder/desc/xrowevent/list/'|ezurl}>{'Descending'|i18n( 'extension/xroweventmanager' )}</a>
{/if}
</p>
</div>

<div class="break"></div>

</div>
</div>

<table class="list" cellspacing="0">
<tr>
    <th class="wide">{'Event'|i18n( 'extension/xroweventmanager' )}</th>
	<th class="tight">{'start'|i18n( 'extension/xroweventmanager' )}</th>
	<th class="tight">{'end'|i18n( 'extension/xroweventmanager' )}</th>
	<th class="tight">{'persons'|i18n( 'extension/xroweventmanager' )}</th>
	<th class="tight">{'participants'|i18n( 'extension/xroweventmanager' )}</th>
</tr>
{foreach $event_list as $key => $event sequence array( bglight, bgdark ) as $seq}

<tr class="{$seq}">
    <td><a href={concat("xrowevent/event/",$event.contentobject_id)|ezurl}>{$event.event_object.name|wash}</a>
	</td>
	<td nowrap="nowrap">{if $event.start_date|gt(0)}{$event.start_date|l10n( shortdatetime )}{else}&nbsp;{/if}</td>
	<td nowrap="nowrap">{if $event.end_date|gt(0)}{$event.end_date|l10n( shortdatetime )}{else}&nbsp;{/if}</td>
	<td class="number" align="right">{$event.person_count}</td>
	<td class="number" align="right">{$event.participants_count}{if $event.max_participants|gt(0)} ({$event.max_participants}){/if}</td>
</tr>
{/foreach}
</table>
{else}
<div class="block">
<p>{'No events.'|i18n( 'extension/xroweventmanager' )}</p>
</div>
{/if}

<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/xrowevent/list'
         item_count=$event_list_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>


{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="break"></div>

</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

