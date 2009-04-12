<?php

$FunctionList = array();

$FunctionList['participant_list'] = array( 'name' => 'participant_list',
                                 'operation_types' => array( 'read' ),
                                 'call_method' => array( 'include_file' => 'extension/xroweventmanager/modules/xrowevent/xroweventfunctioncollection.php',
                                                         'class' => 'xrowEventFunctionCollection',
                                                         'method' => 'fetchParticipants' ),
                                 'parameter_type' => 'standard',
                                 'parameters' => array( array( 'name' => 'event_id',
                                                                'type' => 'integer',
                                                                'required' => true ),
                                                         array( 'name' => 'offset',
                                                                'type' => 'integer',
                                                                'default' => 0,
                                                                'required' => false ),
                                                          array( 'name' => 'limit',
                                                                'type' => 'integer',
                                                                'default' => 10,
                                                                'required' => false ),
                                                          array( 'name' => 'sort_array',
                                                                'type' => 'array',
                                                                'default' => array(),
                                                                'required' => false ),
                                                           ) );

?>
