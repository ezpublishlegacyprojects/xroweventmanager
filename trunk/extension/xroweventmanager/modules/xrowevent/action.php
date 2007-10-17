<?php

include_once( 'lib/ezutils/classes/ezhttptool.php' );
include_once( 'extension/xroweventmanager/classes/xrowevent.php' );
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );

$Module =& $Params['Module'];

$http =& eZHTTPTool::instance();
$ini =& eZINI::instance();

$eventID = 0;
$userID = eZUser::currentUserID();

if ( $http->hasPostVariable( 'EventID' ) and $http->hasPostVariable( 'AddParticipant' ) )
{
    if ( $http->hasPostVariable( 'UserID' ) )
        $userID = $http->postVariable( 'UserID' );
        
    $eventID = $http->postVariable( 'EventID' );
    $event = xrowEvent::fetch( $eventID );
    if ( is_object( $event ) )
        $event->addParticipant( $userID );
    else
        eZDebug::writeError( "Event doesn't exists: $eventID", "xrowevent - add participant" );
}
else if ( $http->hasPostVariable( 'EventID' ) and $http->hasPostVariable( 'RemoveParticipant' ) )
{
    if ( $http->hasPostVariable( 'UserID' ) )
        $userID = $http->postVariable( 'UserID' );
        
    $eventID = $http->postVariable( 'EventID' );
    $event = xrowEvent::fetch( $eventID );
    if ( is_object( $event ) )
        $event->removeParticipant( $userID );
    else
        eZDebug::writeError( "Event doesn't exists: $eventID", "xrowevent - remove participant" );
}

if ( $http->hasSessionVariable( "LastAccessesURI" ) )
    $redirectionURI = $http->sessionVariable( "LastAccessesURI" );
    
$haveRedirectionURI = ( $redirectionURI != '' && $redirectionURI != '/' );

if ( !$haveRedirectionURI )
    $redirectionURI = $ini->variable( 'SiteSettings', 'DefaultPage' );
    
return $Module->redirectTo( $redirectionURI );

?>