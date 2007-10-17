<?php

include_once( 'kernel/classes/ezpersistentobject.php' );
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
include_once( 'kernel/classes/ezcontentobject.php' );

class xrowEventPersons extends eZPersistentObject
{
	function xrowEventPersons( $row )
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
                                        ),
                                                             
                      'keys' => array( 'event_id', 'user_id' ),
                      'function_attributes' => array( 'user_object' => 'userObject',
                                                      'event_object' => 'eventObject' ),
                      'class_name' => 'xrowEventPersons',
                      'sort' => array( 'event_id' => 'asc' ),
                      'relations' => array( 'event_id' => array( 'class' => 'xrowevent',
                                                                 'field' => 'contentobject_id' )
                                          ),
                      'name' => 'xrowevent_persons' );
    }
    
    function fetchPersons( $eventID, $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( xrowEventPersons::definition(),
                                                    null,
                                                    array( 'event_id' => $eventID ),
                                                    null,
                                                    null,
                                                    $asObject );
    }
    
    function countPersons( $eventID )
    {
        $db =& eZDB::instance();
        $sql = "SELECT COUNT(*) counter FROM xrowevent_persons WHERE event_id = '$eventID'";
        $result = $db->arrayQuery( $sql );
        return $result[0]['counter'];
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
    
    function fetchEvents( $userID = false, $asObject = true )
    {
        if ( !$userID )
            $userID = eZUser::currentUserID();

        return eZPersistentObject::fetchObjectList( xrowEventPersons::definition(),
                                                    null,
                                                    array( 'user_id' => $userID ),
                                                    null,
                                                    null,
                                                    $asObject );
    }
    
    function userExists( $userID, $eventID )
    {
        $db =& eZDB::instance();
        $sql = "SELECT COUNT(*) counter FROM xrowevent_persons WHERE user_id = '$userID' AND event_id = '$eventID'";
        $result = $db->arrayQuery( $sql );
        if ( $result[0]['counter'] > 0 )
            return true;
        else
            return false;
    }
    
    function addPerson( $userID, $eventID )
    {
        $db =& eZDB::instance();
        $sql = "SELECT * FROM xrowevent_persons WHERE user_id = '$userID' AND event_id = '$eventID'";
        $result = $db->arrayQuery( $sql );
        
        if ( count( $result ) == 0 )
        {
            $person = new xrowEventPersons( array( 'event_id' => $eventID, 
                                                   'user_id' => $userID ) );
            $person->store();   
        }
        else
            $person = new xrowEventPersons( $result[0] );

        return $person;
    }
    
    function removePerson( $userID, $eventID )
    {
        $db =& eZDB::instance();
        $sql = "DELETE FROM xrowevent_persons WHERE event_id = '$eventID' AND user_id = '$userID'";
        $db->query( $sql );  
    }
    
    function removeEvent( $eventID )
    {
        $db =& eZDB::instance();
        $sql = "DELETE FROM xrowevent_persons WHERE event_id = '$eventID'";
        $db->query( $sql );
    }
}
?>