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
        $custom = array( array( 'operation' => 'count(*)',
                                'name' => 'count' ) );
        
        $result = eZPersistentObject::fetchObjectList( xrowEventPersons::definition(),
                                                       array(),
                                                       array( 'event_id' => $eventID ),
                                                       null, null, false, false, 
                                                       $custom );
                                                    
        return $result[0]['count'];
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
        $custom = array( array( 'operation' => 'count(*)',
                                'name' => 'count' ) );
        
        $result = eZPersistentObject::fetchObjectList( xrowEventPersons::definition(),
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
    
    function fetchUser( $userID, $eventID, $asObject = true )
    {
        return eZPersistentObject::fetchObject( xrowEventPersons::definition(),
                                                null,
                                                array( 'user_id' => $userID,
                                                       'event_id' => $eventID ),
                                                $asObject );
    }
    
    function addPerson( $userID, $eventID )
    {
        $person = xrowEventPersons::fetchUser( $userID, $eventID );
        
        if ( !is_object( $person ) )
        {
            $person = new xrowEventPersons( array( 'event_id' => $eventID, 
                                                   'user_id' => $userID ) );
            $person->store();   
        }
        
        return $person;
    }
    
    function removePerson( $userID, $eventID )
    {
        $cond = array( 'user_id' => $userID, 'event_id' => $eventID );
        
        eZPersistentObject::removeObject( xrowEventPersons::definition(),
                                          $cond );
    }
    
    function removeEvent( $eventID )
    {
        $cond = array( 'event_id' => $eventID );
        
        eZPersistentObject::removeObject( xrowEventPersons::definition(),
                                          $cond );
    }
}
?>