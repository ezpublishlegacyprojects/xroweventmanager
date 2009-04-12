<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

    <div class="attribute-header">
        <h1>{'Join %name'|i18n( 'extension/xroweventmanager', '', hash( '%name', $event_obj.name ) )|wash}</h1>
    </div>
    {if $error}
        <div class="warning">
            <h2>Error</h2>
             <p>
                {"Please shorten your comment. Only %number characters are allowed."|i18n( 'extension/xroweventmanager',,hash('%number',$max_len) )}
             </p>
        </div>
    {/if}
    <p>
        {"You have decided to join this event.
          You are able to add an optional comment to the event."|i18n( 'extension/xroweventmanager' )}
    </p>

    <form method="post" action={concat( "xrowevent/event_comment/", $event.contentobject_id )|ezurl} name="eventcomment">
        <input type="hidden" name="CancelRedirect" value="{$event_obj.main_node.url_alias}" />
        <input type="hidden" name="SuccessRedirect" value="{$event_obj.main_node.url_alias}" />
        <div class="block">
            <div class="element">
                <label>{"Comment"|i18n( 'extension/xroweventmanager' )}</label><div class="labelbreak"></div>
                <textarea cols="40" rows="5" class="box" name="xroweventcomment">{$comment|wash}</textarea>
            </div>
        </div>
        <div class="break"></div>
        <div class="buttonblock">
            <input class="defaultbutton" type="submit" name="xroweventaddcomment" value="{"Join event"|i18n( 'extension/xroweventmanager' )}" />
            <input class="button" type="submit" name="xroweventcancel" value="{"Cancel"|i18n( 'extension/xroweventmanager' )}" />
        </div>
    </form>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>