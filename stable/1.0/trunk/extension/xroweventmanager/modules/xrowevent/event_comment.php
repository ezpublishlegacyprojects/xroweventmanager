<?php

include_once( 'lib/ezutils/classes/ezhttptool.php' );
include_once( 'extension/xroweventmanager/classes/xrowevent.php' );
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
include_once( 'kernel/common/template.php' );

$Module =& $Params['Module'];

$http =& eZHTTPTool::instance();

// Cancel Button
if ( $http->hasPostVariable( 'xroweventcancel' ) )
{
    $redirectionURI = '';
    if ( $http->hasPostVariable( "CancelRedirect" ) )
        $redirectionURI = $http->postVariable( "CancelRedirect" );

    $haveRedirectionURI = ( $redirectionURI != '' && $redirectionURI != '/' );

    if ( !$haveRedirectionURI )
        $redirectionURI = $ini->variable( 'SiteSettings', 'DefaultPage' );

    return $Module->redirectTo( $redirectionURI );
}

$eventID = $Params['EventID'];

$event = xrowEvent::fetch( $eventID );
$user = eZUser::currentUser();
$error = false;

$xini = eZINI::instance( 'xroweventmanager.ini' );
$maxLen = $xini->variable( 'XrowEventManagerSettings', 'CommentLength' );


if ( is_object( $event ) )
{
    if ( $http->hasPostVariable( 'xroweventaddcomment' ) )
    {
        $comment = trim( $http->postVariable( 'xroweventcomment' ) );

        if ( mb_strlen( $comment ) > $maxLen )
            $error = true;

        if ( !$error )
        {
            $event->addParticipant( $user->id(), $comment );

            $redirectionURI = '';
            if ( $http->hasPostVariable( "SuccessRedirect" ) )
                $redirectionURI = $http->postVariable( "SuccessRedirect" );

            $haveRedirectionURI = ( $redirectionURI != '' && $redirectionURI != '/' );

            if ( !$haveRedirectionURI )
                $redirectionURI = $ini->variable( 'SiteSettings', 'DefaultPage' );

            return $Module->redirectTo( $redirectionURI );
        }
    }

    $tpl =& templateInit();

    $tpl->setVariable( 'comment', $comment );
    $tpl->setVariable( 'event', $event );
    $tpl->setVariable( 'error', $error );
    $tpl->setVariable( 'event_obj', $event->eventObject() );
    $tpl->setVariable( 'current_user', $user );
    $tpl->setVariable( 'max_len', $maxLen );

    $Result = array();
    $Result['path'] = array ( array( 'text' => ezi18n( 'extension/xroweventmanager', 'Join event' ),
                              'url' => false ) );

    $Result['content'] = $tpl->fetch( 'design:xrowevent/event_comment.tpl' );
}
else
{
    return $Module->handleError( EZ_ERROR_KERNEL_ACCESS_DENIED, 'kernel' );
}

?>