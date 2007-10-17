{* xrowevent - Line view *}
<div class="content-view-line">
    <div class="class-article">

    <h2><a href={$node.url_alias|ezurl}>{$node.data_map.title.content|wash}</a></h2>

    {if $node.data_map.xrowevent.has_content}
        <p>{$node.data_map.xrowevent.content.start_date|l10n(datetime)} - 
           {$node.data_map.xrowevent.content.end_date|l10n(datetime)} - 
        </p>
    </div>
    
    <div class="attribute-short">
        {attribute_view_gui attribute=$node.data_map.description}
    </div>
    
    <div class="attribute-link">
        <p><a href={$node.url_alias|ezurl}>{"Read more..."|i18n("extension/xroweventmanager")}</a></p>
    </div>
    
</div>