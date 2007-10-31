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
    $_SESSION['EXTRACTCSV_OBJECTID_ARRAY'] = $event->fetchParticipantsObjectIDArray();
    
    $Module->redirectTo( 'extract/csv' );

}
else
{
    return $Module->handleError( EZ_ERROR_KERNEL_ACCESS_DENIED, 'kernel' );
}

?>