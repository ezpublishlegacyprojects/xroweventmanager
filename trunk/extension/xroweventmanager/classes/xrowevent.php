<?php

include_once( 'kernel/classes/ezpersistentobject.php' );
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
include_once( 'extension/xroweventmanager/classes/xrowevent_persons.php' );
include_once( 'extension/xroweventmanager/classes/xrowevent_participants.php' );

define( 'XROW_EVENT_STATUS_DRAFT', 0 );
define( 'XROW_EVENT_STATUS_PLACES_AVAILABLE', 1 );
define( 'XROW_EVENT_STATUS_NO_PLACES', 2 );
define( 'XROW_EVENT_STATUS_EVENT_CANCELED', 3 );

class xrowEvent extends eZPersistentObject
{
	function xrowEvent( $row )
	{
		$this->eZPersistentObject( $row );
	}
	
	function definition()
    {
        return array( 'fields' => array( 'start_date' => array( 'name' => 'StartDate',
                                                        'datatype' => 'ingeger',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'end_date' => array( 'name' => 'EndDate',
                                                        'datatype' => 'ingeger',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'max_participants' => array( 'name' => 'MaxParticipants',
                                                        'datatype' => 'ingeger',
                                                        'default' => 1,
                                                        'required' => true ),               
                                         'status' => array( 'name' => 'Status',
                                                        'datatype' => 'ingeger',
                                                        'default' => 0,
                                                        'required' => true ),               
                                         "contentobject_id" => array( 'name' => "ContentObjectID",
                                                                      'datatype' => 'integer',
                                                                      'default' => 0,
                                                                      'required' => true,
                                                                      'foreign_class' => 'eZContentObject',
                                                                      'foreign_attribute' => 'id',
                                                                      'multiplicity' => '1..*' ),
                                          ),
                                                             
                      'keys' => array( 'contentobject_id' ),
                      'function_attributes' => array( 'participants' => 'fetchParticipants',
                                                      'participants_count' => 'countParticipants',
                                                      'persons' => 'fetchPersons',
                                                      'person_count' => 'countPersons',
                                                      'status_array' => 'statusArray',
                                                      'status_text' => 'statusText',
                                                      'person_user_exists' => 'personUserExists',
                                                      'participant_user_exists' => 'participantUserExists',
                                                      'event_length' => 'eventLength',
                                                      'event_object' => 'eventObject'
                                                       ),
                      'class_name' => 'xrowEvent',
                      'sort' => array( 'start_date' => 'asc' ),
                      'name' => 'xrowevent_event' );
    }
    
    function personUserExists()
    {
        $userID = eZUser::currentUserID();
        return xrowEventPersons::userExists( $userID, $this->attribute( 'contentobject_id' ) );
    }
    
    function participantUserExists()
    {
        $userID = eZUser::currentUserID();
        return xrowEventParticipants::userExists( $userID, $this->attribute( 'contentobject_id' ) );
    }
    
    function eventLength()
    {
        return $this->attribute( 'end_date' ) - $this->attribute( 'start_date' );
    }
    
    function countParticipants()
    {
        return xrowEventParticipants::countParticipants( $this->attribute( 'contentobject_id' ) );
    }
    
    function countPersons()
    {
        return xrowEventPersons::countPersons( $this->attribute( 'contentobject_id' ) );
    }
    
    function fetch( $contentObjectID, $asObject = true )
    {
        return eZPersistentObject::fetchObject( xrowEvent::definition(),
                                                null,
                                                array( 'contentobject_id' => $contentObjectID ),
                                                $asObject );
    }
    
    function fetchParticipants()
    {
        return xrowEventParticipants::fetchParticipants( $this->attribute( 'contentobject_id' ), true );
    }
    
    function fetchPersons()
    {
        return xrowEventPersons::fetchPersons( $this->attribute( 'contentobject_id' ), true );
    }
    
    function addPerson( $userID )
    {
        return xrowEventPersons::addPerson( $userID, $this->attribute( 'contentobject_id' ) );
    }
    
    function removePerson( $userID )
    {
        xrowEventPersons::removePerson( $userID, $this->attribute( 'contentobject_id' ) );
    }
    
    function addParticipant( $userID )
    {
        $participantCount = $this->countParticipants();
        $maxParticipants = $this->attribute( 'max_participants' );
        if ( $participantCount < $maxParticipants )
        {
            $result = xrowEventParticipants::addParticipant( $userID, $this->attribute( 'contentobject_id' ) );
            // no dupe check before
            $participantCount = $this->countParticipants();
            if ( $participantCount == $maxParticipants )
            {
                $this->setAttribute( 'status', XROW_EVENT_STATUS_NO_PLACES );
                $this->store();
            }
            return $result;
        }
        else
            eZDebug::writeError( 'Max. amount of participant already reached', 'xrowEvent::addParticipant()' );
        
        return false;
    }
    
    function removeParticipant( $userID )
    {
        xrowEventParticipants::removeParticipant( $userID, $this->attribute( 'contentobject_id' ) );
        $participantCount = $this->countParticipants();
        $status = $this->attribute( 'status' );
        if ( $status == XROW_EVENT_STATUS_NO_PLACES )
        {
            $maxParticipants = $this->attribute( 'max_participants' );
            if ( $participantCount < $maxParticipants )
            {
                $status = XROW_EVENT_STATUS_PLACES_AVAILABLE;
                $this->setAttribut( 'status', $status );
                $this->store();
            }
        }
    }
    
    function saveEvent( $data )
    {
        $event = xrowEvent::fetch( $data['contentobject_id'] );
        if ( !is_object( $event ) )
        {
            $event = new xrowevent( $data );
        }
        else
        {
            $event->setAttribute( 'start_date', $data['start_date'] );
            $event->setAttribute( 'end_date', $data['end_date'] );
            $event->setAttribute( 'max_participants', $data['max_participants'] );
            $event->setAttribute( 'status', $data['status'] );
        }
        
        $event->store();
        return $event;
    }
    
    function removeEvent( $contentObjectID = false )
    {
        if ( !$contentObjectID )
            $contentObjectID = $this->attribute( 'contentobject_id' );
        
        $db =& eZDB::instance();
        $db->begin();
        xrowEventPersons::removeEvent( $contentObjectID );
        xrowEventParticipants::removeEvent( $contentObjectID );
        
        $sql = "DELETE FROM xrowevent_event WHERE contentobject_id = '$contentObjectID'";
        $db->query( $sql );
        
        $db->commit();
    }
    
    function statusArray()
    {
        return array( XROW_EVENT_STATUS_PLACES_AVAILABLE => ezi18n( 'extension/xroweventmanager', "Places available" ),
                      XROW_EVENT_STATUS_NO_PLACES => ezi18n( 'extension/xroweventmanager', "No places available" ),
                      XROW_EVENT_STATUS_EVENT_CANCELED => ezi18n( 'extension/xroweventmanager', "Event canceled" ) 
                     );
    }
    
    function statusText()
    {
        $statusArray = $this->statusArray();
        $status = $this->attribute( 'status' );
        return $statusArray[$status];
    }
    
    function fetchEvents( $asObject, $offset, $limit, $sortField, $sortOrder )
    {
        $user =& eZUser::currentUser();
        $userID = $user->id();
        $isAdmin = $user->hasAccessTo( 'xrowevent', 'admin' );
        $isManager = $user->hasAccessTo( 'xrowevent', 'manage' );
        
        if ( !$isAdmin and !$isManager )
            return array();
            
        if ( $sortField == 'start_date' )
            $sortFieldSql = 'a.start_date';
        else 
            $sortFieldSql = 'c.name';
            
        $db =& eZDB::instance();
        if ( !$isAdmin )
        {
            $sql = "SELECT 
                        a.*
                    FROM 
                        xrowevent_event a,
                        xrowevent_persons b,
                        ezcontentobject c
                    WHERE
                        a.start_date > 0 AND
                        a.contentobject_id = b.event_id AND
                        b.user_id = '$userID' AND
                        a.contentobject_id = c.id AND
                        c.status = 1
                    ORDER BY
                        $sortFieldSql $sortOrder
                    ";
        }
        else 
        {
             $sql = "SELECT 
                        a.*
                    FROM 
                        xrowevent_event a,
                        ezcontentobject c
                    WHERE
                        a.start_date > 0 AND
                        a.contentobject_id = c.id AND
                        c.status = 1
                    ORDER BY
                        $sortFieldSql $sortOrder
                    ";
        }
        
        $result = $db->arrayQuery( $sql, array( 'offset' => $offset, 'limit' => $limit ) );
        
        $eventArray = array();
        if ( $asObject )
        {
            foreach( $result as $key => $item )
            {
                $eventArray[] = new xrowEvent( $item );
            }
            return $eventArray;
        }
        else
            return $result;
    }
    
    function eventCount()
    {
        $user =& eZUser::currentUser();
        $userID = $user->id();
        $isAdmin = $user->hasAccessTo( 'xrowevent', 'admin' );
        $isManager = $user->hasAccessTo( 'xrowevent', 'manage' );
        
        if ( !$isAdmin and !$isManager )
            return 0;
            
        $db =& eZDB::instance();
        if ( !$isAdmin )
        {
            $sql = "SELECT 
                        COUNT(*) counter
                    FROM 
                        xrowevent_event a,
                        xrowevent_persons b,
                        ezcontentobject c
                    WHERE
                        a.start_date > 0 AND
                        a.contentobject_id = b.event_id AND
                        b.user_id = '$userID' AND
                        a.contentobject_id = c.id AND
                        c.status = 1
                    ";
        }
        else 
        {
             $sql = "SELECT 
                        COUNT(*) counter
                    FROM 
                        xrowevent_event a,
                        ezcontentobject c
                    WHERE
                        a.start_date > 0 AND
                        a.contentobject_id = c.id AND
                        c.status = 1
                    ";
        }
        
        $result = $db->arrayQuery( $sql );
        
        return $result[0]['counter'];
    }
    
    function eventObject()
    {
        $coID = $this->attribute( 'contentobject_id' );      
        return eZContentObject::fetch( $coID );
    }
}
?>