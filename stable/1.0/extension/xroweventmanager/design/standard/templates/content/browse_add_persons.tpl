{let version=fetch( content, version, hash( object_id, $browse.persistent_data.object_id, version_id, $browse.persistent_data.version_id ) )}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Choose users that you wish to add to <%version_name>'|i18n( 'extension/xroweventmanager',, hash( '%version_name', $version.version_name ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<div class="block">
<p>{'Use the checkboxes to choose the users that you wish to add to <%version_name>.'|i18n( 'extension/xroweventmanager',, hash( '%version_name', $version.version_name ) )|wash}</p>
<p>{'Navigate using the available tabs (above), the tree menu (left) and the content list (middle).'|i18n( 'extension/xroweventmanager' )}</p>
</div>

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>

{/let}
