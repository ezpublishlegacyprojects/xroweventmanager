<?php

include_once( 'lib/ezutils/classes/ezhttptool.php' );
include_once( 'extension/xroweventmanager/classes/xrowevent.php' );
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
include_once( 'kernel/common/template.php' );

$Module =& $Params['Module'];

$http =& eZHTTPTool::instance();
$ini =& eZINI::instance();

$tpl =& templateInit();

$eventID = $Params['EventID'];

$event = xrowEvent::fetch( $eventID );
$user =& eZUser::currentUser();

$isAdminArray = $user->hasAccessTo( 'xrowevent', 'admin' );
if ( $isAdminArray['accessWord'] != 'no' )
    $isAdmin = true;
else
    $isAdmin = false;

if ( ( $event->personUserExists() or
       $isAdmin )
      and is_object( $event ) )
{
    $tpl->setVariable( 'event_id', $eventID );
    $tpl->setVariable( 'event', $event );
    
    $path = array();
    $path[] = array( 'text' => ezi18n( 'extension/xroweventmanager', 'Export event' ),
                     'url' => false );
    
    $Result = array();
    $Result['path'] =& $path;
    
    $Result['content'] =& $tpl->fetch( 'design:xrowevent/export.tpl' );

}
else
{
    return $Module->handleError( EZ_ERROR_KERNEL_ACCESS_DENIED, 'kernel' );
}

?>