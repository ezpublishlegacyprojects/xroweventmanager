{* xrow event - Full view *}
{* disable view cache for this page! *}
{set-block scope=root variable=cache_ttl}0{/set-block}
<div class="content-view-full">
    <div class="class-xrowevent">

        <h1>{$node.data_map.title.content|wash()}</h1>

        {if $node.data_map.description.content.is_empty|not}
            <div class="attribute-long">
                {attribute_view_gui attribute=$node.data_map.description}
            </div>
        {/if}

        {if $node.data_map.xrowevent.has_content}
            {attribute_view_gui attribute=$node.data_map.xrowevent}
        {/if}

    </div>
</div>
