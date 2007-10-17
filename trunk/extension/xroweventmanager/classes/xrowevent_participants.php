<?php

include_once( 'kernel/classes/ezpersistentobject.php' );
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
include_once( 'lib/ezlocale/classes/ezdatetime.php' );
include_once( 'kernel/classes/ezcontentobject.php' );

class xrowEventParticipants extends eZPersistentObject
{
	function xrowEventParticipants( $row )
	{
		$this->eZPersistentObject( $row );
	}
	
	function definition()
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
                                          ),
                                                             
                      'keys' => array( 'contentobject_id' ),
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
    
    function countParticipants( $eventID )
    {
        $db =& eZDB::instance();
        $sql = "SELECT COUNT(*) counter FROM xrowevent_participants WHERE event_id = '$eventID'";
        $result = $db->arrayQuery( $sql );
        return $result[0]['counter'];
    }
    
    function fetchParticipants( $eventID, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( xrowEventParticipants::definition(),
                                                    null,
                                                    array( 'event_id' => $eventID ),
                                                    null,
                                                    null,
                                                    $asObject );
    }
    
    function fetchEvents( $userID, $asObject = true )
    {
        if ( !$userID )
            $userID = eZUser::currentUserID();
        
        return eZPersistentObject::fetchObjectList( xrowEventParticipants::definition(),
                                                    null,
                                                    array( 'user_id' => $userID ),
                                                    null,
                                                    null,
                                                    $asObject );
    }
    
    function addParticipant( $userID, $eventID, $created = false )
    {
        $db =& eZDB::instance();
        $sql = "SELECT * FROM xrowevent_participants WHERE user_id = '$userID' AND event_id = '$eventID'";
        $result = $db->arrayQuery( $sql );
        if ( count( $result ) == 0 )
        {
            if ( !$created )
            {
                
                $dateTime = new eZDateTime();
                $created = $dateTime->timeStamp();
            }  
            $participant = new xrowEventParticipants( array( 'event_id' => $eventID, 
                                                            'user_id' => $userID, 
                                                            'created' => $created ) );
            $participant->store();   
        }
        else
            $person = new xrowEventParticipants( $result[0] );
        
        return $participant;
    }
    
    function removeParticipant( $userID, $eventID )
    {
        $db =& eZDB::instance();
        $sql = "DELETE FROM xrowevent_participants WHERE event_id = '$eventID' AND user_id = '$userID'";
        $db->query( $sql );   
    }
    
    function removeEvent( $eventID )
    {
        $db =& eZDB::instance();
        $sql = "DELETE FROM xrowevent_participants WHERE event_id = '$eventID'";
        $db->query( $sql );
    }
    
    function userExists( $userID, $eventID )
    {
        $db =& eZDB::instance();
        $sql = "SELECT COUNT(*) counter FROM xrowevent_participants WHERE user_id = '$userID' AND event_id = '$eventID'";
        $result = $db->arrayQuery( $sql );
        if ( $result[0]['counter'] > 0 )
            return true;
        else
            return false;
    }
}
?>