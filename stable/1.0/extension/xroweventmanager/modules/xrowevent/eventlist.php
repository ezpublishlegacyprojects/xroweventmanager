<?php

include_once( 'lib/ezutils/classes/ezhttptool.php' );
include_once( 'extension/xroweventmanager/classes/xrowevent.php' );
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
include_once( 'kernel/common/template.php' );
include_once( 'kernel/classes/ezpreferences.php' );

$Module =& $Params['Module'];

$http =& eZHTTPTool::instance();
$ini =& eZINI::instance();

$tpl =& templateInit();

$offset = $Params['Offset'];
$limit = 15;


if( eZPreferences::value( 'admin_eventlist_sortfield' ) )
{
    $sortField = eZPreferences::value( 'admin_eventlist_sortfield' );
}

if ( !isset( $sortField ) || ( ( $sortField != 'start_date' ) && ( $sortField!= 'event_name' ) ) )
{
    $sortField = 'start_date';
}

if( eZPreferences::value( 'admin_eventlist_sortorder' ) )
{
    $sortOrder = eZPreferences::value( 'admin_eventlist_sortorder' );
}

if ( !isset( $sortOrder ) || ( ( $sortOrder != 'asc' ) && ( $sortOrder!= 'desc' ) ) )
{
    $sortOrder = 'asc';
}

$eventArray = xrowEvent::fetchEvents( true, $offset, $limit, $sortField, $sortOrder );
$eventCount = xrowEvent::eventCount();

$tpl->setVariable( 'event_list', $eventArray );
$tpl->setVariable( 'event_list_count', $eventCount );
$tpl->setVariable( 'limit', $limit );

$viewParameters = array( 'offset' => $offset );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'sort_field', $sortField );
$tpl->setVariable( 'sort_order', $sortOrder );

$path = array();
$path[] = array( 'text' => ezi18n( 'extension/xroweventmanager', 'Event list' ),
                 'url' => false );

$Result = array();
$Result['path'] =& $path;

$Result['content'] =& $tpl->fetch( 'design:xrowevent/eventlist.tpl' );
$Result['left_menu'] = 'design:parts/xroweventmanager/menu.tpl';


?>