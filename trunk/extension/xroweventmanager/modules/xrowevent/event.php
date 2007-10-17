<?php

include_once( 'lib/ezutils/classes/ezhttptool.php' );
include_once( 'extension/xroweventmanager/classes/xrowevent.php' );
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
include_once( 'kernel/common/template.php' );

$Module =& $Params['Module'];

$http =& eZHTTPTool::instance();
$ini =& eZINI::instance();

$tpl =& templateInit();

$offset = $Params['Offset'];

$eventID = $Params['EventID'];

$event = xrowEvent::fetch( $eventID );
$user =& eZUser::currentUser();

if ( ( $event->personUserExists() or
     $user->hasAccessTo( 'xrowevent', 'admin' ) )
     and is_object( $event ) )
{
    $viewParameters = array( 'offset' => $offset );
    $tpl->setVariable( 'view_parameters', $viewParameters );
    $tpl->setVariable( 'event', $event );
    $tpl->setVariable( 'participants_count', $event->countParticipants() );
    $tpl->setVariable( 'participants_list', $event->fetchParticipants() );
    $tpl->setVariable( 'person_count', $event->countPersons() );
    
    $path = array();
    $path[] = array( 'text' => ezi18n( 'extension/xroweventmanager', 'Event details' ),
                     'url' => false );
    
    $Result = array();
    $Result['path'] =& $path;
    
    $Result['content'] =& $tpl->fetch( 'design:xrowevent/event.tpl' );

}
else
{
    return $Module->handleError( EZ_ERROR_KERNEL_ACCESS_DENIED, 'kernel' );
}

?>