<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{$event.event_object.name|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

<table class="list" cellspacing="0">
<tr>
    <th class="tight">&nbsp;</th>
    <th class="wide">{'Person(s)'|i18n( 'extension/xroweventmanager' )}</th>
</tr>
{foreach $event.persons as $key => $person sequence array( bglight, bgdark ) as $seq}
<tr class="{$seq}">
    <td>&nbsp;</td>
    <td><a href={$person.user_object.main_node.url_alias|ezurl}>{$person.user_object.name|wash}</a>
	</td>
</tr>
{/foreach}
</table>

<p>&nbsp;</p>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">
{if $participants_list|gt(0)}
<table class="list" cellspacing="0">
<tr>
    <th class="tight">&nbsp;</th>
    <th class="wide">{'Participant'|i18n( 'extension/xroweventmanager' )}</th>
	<th class="tight">{'joined'|i18n( 'extension/xroweventmanager' )}</th>
</tr>
{foreach $participants_list as $key => $participant sequence array( bglight, bgdark ) as $seq}
<tr class="{$seq}">
    <td>&nbsp;</td>
    <td><a href={$participant.user_object.main_node.url_alias|ezurl}>{$participant.user_object.name|wash}</a>
	</td>
	<td nowrap="nowrap">{if $participant.created|gt(0)}{$participant.created|l10n( shortdatetime )}{else}&nbsp;{/if}</td>
</tr>
{/foreach}
</table>
{else}
    <div class="block">
    <p>{'No participants.'|i18n( 'extension/xroweventmanager' )}</p>
    </div>
{/if}

<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/xrowevent/event'
         item_count=$participants_count
         view_parameters=$view_parameters
         item_limit=15}
</div>


{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="break"></div>

</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

