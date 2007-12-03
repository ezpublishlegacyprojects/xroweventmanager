{* xrow_event - Full view *}
{* disable view cache for this page! *}
{set-block scope=root variable=cache_ttl}0{/set-block}

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">


<div class="content-view-full">
    <div class="class-xrow_event">

        <div class="attribute-header">
            <h1>{$node.data_map.title.content|wash()}</h1>
        </div>

        {if $node.data_map.description.content.is_empty|not}
            <div class="attribute-long">
                {attribute_view_gui attribute=$node.data_map.description}
            </div>
        {/if}

        {if $node.data_map.xrow_event.has_content}
            {attribute_view_gui attribute=$node.data_map.xrow_event}
        {/if}

    </div>
</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>