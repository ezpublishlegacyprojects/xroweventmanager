<?php
$Module = array( "name" => "xrow event manager" );

$ViewList = array();

$ViewList['action'] = array(
    'script' => 'action.php',
    'functions' => array( 'use' )
);

$ViewList['list'] = array(
    'script' => 'eventlist.php',
    'functions' => array( 'manage' ),
    'default_navigation_part' => 'xroweventnavigationpart',
    'ui_context' => 'content',
    "unordered_params" => array( "offset" => "Offset" ),
    "params" => array() );

$ViewList['event'] = array(
    'script' => 'event.php',
    'functions' => array( 'manage' ),
    'default_navigation_part' => 'xroweventnavigationpart',
    'ui_context' => 'content',
    "unordered_params" => array( "offset" => "Offset" ),
    "params" => array( 'eventid' => 'EventID' ) );

$FunctionList = array();
$FunctionList['use'] = array();
$FunctionList['manage'] = array();
$FunctionList['administrate'] = array();
?>