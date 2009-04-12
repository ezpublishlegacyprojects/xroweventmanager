<?php

class xrowEventParticipants extends eZPersistentObject
{
	function xrowEventParticipants( $row )
	{
		$this->eZPersistentObject( $row );
	}

	static function definition()
    {
        return array( 'fields' => array( "event_id" => array( 'name' => "EventID",
                                                                      'datatype' => 'integer',
                                                                      'default' => 0,
                                                                      'required' => true,
                                                                      'foreign_class' => 'xrowevent',
                                                                      'foreign_attribute' => 'contentobject_id',
                                                                      'multiplicity' => '1..*' ),
                                         'user_id' => array( 'name' => "UserID",
                                                                      'datatype' => 'integer',
                                                                      'default' => 0,
                                                                      'required' => true,
                                                                      'foreign_class' => 'eZUser',
                                                                      'foreign_attribute' => 'contentobject_id',
                                                                      'multiplicity' => '1..*' ),
                                         'created' => array( 'name' => 'created',
                                                        'datatype' => 'ingeger',
                                                        'default' => 0,
                                                        'required' => true ),
                                        'comment' => array( 'name' => 'comment',
                                                        'datatype' => 'string',
                                                        'default' => '',
                                                        'required' => false ),
                                          ),

                      'keys' => array( 'event_id' ),
                      'function_attributes' => array( 'user_object' => 'userObject',
                                                      'event_object' => 'eventObject' ),
                      'class_name' => 'xrowEventParticipants',
                      'sort' => array( 'created' => 'asc' ),
                      'relations' => array( 'event_id' => array( 'class' => 'xrowevent',
                                                                 'field' => 'contentobject_id' )
                                          ),
                      'name' => 'xrowevent_participants'
                      );
    }

    function userObject()
    {
        $userID = $this->attribute( 'user_id' );
        return eZContentObject::fetch( $userID );
    }

    function eventObject()
    {
        $eventID = $this->attribute( 'event_id' );
        return eZContentObject::fetch( $eventID );
    }

    static function countParticipants( $eventID )
    {
        $custom = array( array( 'operation' => 'count(*)',
                                'name' => 'count' ) );

        $result = eZPersistentObject::fetchObjectList( xrowEventParticipants::definition(),
                                                       array(),
                                                       array( 'event_id' => $eventID ),
                                                       null, null, false, false,
                                                       $custom );

        return $result[0]['count'];
    }

    static function fetchParticipants( $eventID, $asObject = true, $offset = 0, $limit = false, $sortArray = array() )
    {
        $limitArray = null;
        if ( $limit > 0 )
            $limitArray = array( 'offset' => $offset, 'length' => $limit );

        $sorts = array( 'created' => 'asc' );
        if ( count( $sortArray ) > 0 )
        {
            $sorts = array();
            foreach( $sortArray as $key => $sortItem )
            {
                $sorts[ $sortItem[0] ] = $sortItem[1];
            }
        }

        return eZPersistentObject::fetchObjectList( xrowEventParticipants::definition(),
                                                    null,
                                                    array( 'event_id' => $eventID ),
                                                    $sorts,
                                                    $limitArray,
                                                    $asObject );
    }

    function fetchEvents( $userID, $asObject = true )
    {
        if ( !$userID )
            $userID = eZUser::currentUserID();

        return eZPersistentObject::fetchObjectList( xrowEventParticipants::definition(),
                                                    null,
                                                    array( 'user_id' => $userID ),
                                                    null, null,
                                                    $asObject );
    }

    static function fetchUser( $userID, $eventID, $asObject = true )
    {
        return eZPersistentObject::fetchObject( xrowEventParticipants::definition(),
                                                null,
                                                array( 'user_id' => $userID,
                                                       'event_id' => $eventID ),
                                                $asObject );
    }

    static function addParticipant( $userID, $eventID, $created = false, $comment = '' )
    {
        $participant = xrowEventParticipants::fetchUser( $userID, $eventID );

        if ( !is_object( $participant ) )
        {
            if ( !$created )
            {
                $dateTime = new eZDateTime();
                $created = $dateTime->timeStamp();
            }

            $participant = new xrowEventParticipants( array( 'event_id' => $eventID,
                                                             'user_id' => $userID,
                                                             'created' => $created,
                                                             'comment' => $comment ) );
            $participant->store();
        }

        return $participant;
    }

    function removeParticipant( $userID, $eventID )
    {
        $cond = array( 'user_id' => $userID, 'event_id' => $eventID );

        eZPersistentObject::removeObject( xrowEventParticipants::definition(),
                                          $cond );
    }

    static function removeEvent( $eventID )
    {
        $cond = array( 'event_id' => $eventID );

        eZPersistentObject::removeObject( xrowEventParticipants::definition(),
                                          $cond );
    }

    static function userExists( $userID, $eventID )
    {
        $custom = array( array( 'operation' => 'count(*)',
                                'name' => 'count' ) );

        $result = eZPersistentObject::fetchObjectList( xrowEventParticipants::definition(),
                                                       array(),
                                                       array( 'user_id' => $userID,
                                                              'event_id' => $eventID ),
                                                       null, null, false, false,
                                                       $custom );

        if ( $result[0]['count'] > 0 )
            return true;
        else
            return false;
    }
}
?>