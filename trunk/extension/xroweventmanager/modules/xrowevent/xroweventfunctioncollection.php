<?php

include_once( 'kernel/error/errors.php' );
include_once( 'extension/xroweventmanager/classes/xrowevent_participants.php');

class xrowEventFunctionCollection
{
    function fetchParticipants( $event_id, $offset = 0, $limit = 10, $sort_array = array() )
    {
        $result = array( 'result' => xrowEventParticipants::fetchParticipants( $event_id, true, $offset, $limit, $sort_array ) );
        return $result;
    }
}

?>
