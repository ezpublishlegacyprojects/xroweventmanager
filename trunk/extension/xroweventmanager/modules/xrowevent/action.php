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
    {
        if ( $event->attribute( 'comment' ) == 0 )
            $event->addParticipant( $userID );
        else
            return $Module->redirectToView( 'event_comment', array( $eventID ) );
    }
    else
        eZDebug::writeError( "Event doesn't exists: $eventID", "xrowevent - add participant" );
}
else if ( $http->hasPostVariable( 'EventID' ) and $http->hasPostVariable( 'RemoveParticipant' ) )
{
    $eventID = $http->postVariable( 'EventID' );
    $event = xrowEvent::fetch( $eventID );
    if ( is_object( $event ) )
    {
        $isAllowed = false;
        if ( $http->hasPostVariable( 'UserID' ) )
        {
            $newUserID = $http->postVariable( 'UserID' );
            if ( $userID != $newUserID )
            {
                $isAdminArray = $user->hasAccessTo( 'xrowevent', 'admin' );
                if ( $isAdminArray['accessWord'] != 'no' )
                    $isAdmin = true;
                else
                    $isAdmin = false;

                if ( $isAdmin or
                     $event->personUserExists() )
                {
                    $userID = $newUserID;
                    $isAllowed = true;
                }
            }
            else
                $isAllowed = true;
        }
        else
            $isAllowed = true;

        if ( $isAllowed )
            $event->removeParticipant( $userID );
    }
    else
        eZDebug::writeError( "Event doesn't exists: $eventID", "xrowevent - remove participant" );
}
else if ( $http->hasPostVariable( 'EventID' ) and $http->hasPostVariable( 'RemoveParticipantListButton' ) )
{
    $deleteIDArray = array();
    if ( $http->hasPostVariable( 'DeleteIDArray' ) )
        $deleteIDArray = $http->postVariable( 'DeleteIDArray' );

    $eventID = $http->postVariable( 'EventID' );


    $user =& eZUser::currentUser();
    $isAdminArray = $user->hasAccessTo( 'xrowevent', 'admin' );
    if ( $isAdminArray['accessWord'] != 'no' )
        $isAdmin = true;
    else
        $isAdmin = false;

    if ( $isAdmin or $event->personUserExists() )
    {
        $event = xrowEvent::fetch( $eventID );
        if ( is_object( $event ) )
        {
            foreach( $deleteIDArray as $key => $item )
            {
                $event->removeParticipant( $item );
            }
        }
        else
            eZDebug::writeError( "Event doesn't exists: $eventID", "xrowevent - remove participants" );
    }
}
else if ( $http->hasPostVariable( 'EventID' ) and $http->hasPostVariable( 'CancelEventButton' ) )
{
    $eventID = $http->postVariable( 'EventID' );
    $event = xrowEvent::fetch( $eventID );
    if ( is_object( $event ) )
    {
        $user =& eZUser::currentUser();
        $isAdminArray = $user->hasAccessTo( 'xrowevent', 'admin' );
        if ( $isAdminArray['accessWord'] != 'no' )
            $isAdmin = true;
        else
            $isAdmin = false;

        if ( $isAdmin or $event->personUserExists() )
        {
            $event->setAttribute( 'status', xrowEvent::STATUS_EVENT_CANCELED );
            $event->store();
        }
    }
    else
        eZDebug::writeError( "Event doesn't exists: $eventID", "xrowevent - change event status" );
}
else if ( $http->hasPostVariable( 'EventID' ) and $http->hasPostVariable( 'ActivateEventButton' ) )
{
    $eventID = $http->postVariable( 'EventID' );
    $event = xrowEvent::fetch( $eventID );
    if ( is_object( $event ) )
    {
        $user =& eZUser::currentUser();
        $isAdminArray = $user->hasAccessTo( 'xrowevent', 'admin' );
        if ( $isAdminArray['accessWord'] != 'no' )
            $isAdmin = true;
        else
            $isAdmin = false;

        if ( $isAdmin or $event->personUserExists() )
        {
            if ( $event->attribute( 'max_participants' ) > $event->countParticipants() )
                $event->setAttribute( 'status', xrowEvent::STATUS_PLACES_AVAILABLE );
            else
                $event->setAttribute( 'status', xrowEvent::STATUS_NO_PLACES );

            $event->store();
        }
    }
    else
        eZDebug::writeError( "Event doesn't exists: $eventID", "xrowevent - change event status" );
}
else if ( $http->hasPostVariable( 'EventID' ) and $http->hasPostVariable( 'EditEventButton' ) )
{
    $eventID = $http->postVariable( 'EventID' );
    $eventObj = eZContentObject::fetch( $eventID );
    if ( is_object( $eventObj ) )
    {
        $lang = $eventObj->currentLanguage();
        if ( $http->hasPostVariable( 'RedirectURIAfterPublish' ) )
        {
            $http->setSessionVariable( 'RedirectURIAfterPublish', $http->postVariable( 'RedirectURIAfterPublish' ) );
        }
        return $Module->redirect( 'content', 'edit', array( $eventID, '', $lang ) );
    }
}
else if ( $http->hasPostVariable( 'EventID' ) and $http->hasPostVariable( 'ExportEventButton' ) )
{
    $eventID = $http->postVariable( 'EventID' );
    if ( $http->hasPostVariable( 'RedirectURIAfterExport' ) )
    {
        $http->setSessionVariable( 'RedirectURIAfterExport', $http->postVariable( 'RedirectURIAfterExport' ) );
    }
    return $Module->redirect( 'xrowevent', 'export', array( $eventID ) );
}

if ( $http->hasSessionVariable( "LastAccessesURI" ) )
    $redirectionURI = $http->sessionVariable( "LastAccessesURI" );

$haveRedirectionURI = ( $redirectionURI != '' && $redirectionURI != '/' );

if ( !$haveRedirectionURI )
    $redirectionURI = $ini->variable( 'SiteSettings', 'DefaultPage' );

return $Module->redirectTo( $redirectionURI );

?>