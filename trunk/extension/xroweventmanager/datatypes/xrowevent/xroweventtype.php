<?php

require_once ( 'kernel/common/i18n.php' );

class xrowEventType extends eZDataType
{
	const DATA_TYPE_STRING = 'xrowevent';
	const DEFAULT_EMTPY = 0;
	const DEFAULT_START = 'data_int1';
	const DEFAULT_END = 'data_int2';
	const DEFAULT_MAX = 'data_int3';

    function xrowEventType()
    {
        $this->eZDataType( xrowEventType::DATA_TYPE_STRING,
                           ezi18n( 'extension/xroweventmanager', "xrow Event", 'Datatype name' ),
                           array( 'serialize_supported' => false,
                                  'translation_allowed' => false ) );

    }

    /*!
     Private method only for use inside this class
    */
    function validateEventHTTPInput( $startday,
                                     $startmonth,
                                     $startyear,
                                     $starthour,
                                     $startminute,
                                     $endday,
                                     $endmonth,
                                     $endyear,
                                     $endhour,
                                     $endminute,
                                     $maxparticipants,
                                     $status,
                                     $comment,
                                     &$contentObjectAttribute )
    {
        include_once( 'lib/ezutils/classes/ezdatetimevalidator.php' );

        $startCheck = false;
        $endCheck = false;

        if ( !( $startyear == '' and $startmonth == ''and $startday == '' and
                $starthour == '' and $startminute == '' ) )
        {
            $state = eZDateTimeValidator::validateDate( $startday, $startmonth, $startyear );
            if ( $state == eZInputValidator::STATE_INVALID )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'extension/xroweventmanager',
                                                                     'Start date is not valid.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            $state = eZDateTimeValidator::validateTime( $starthour, $startminute );
            if ( $state == eZInputValidator::STATE_INVALID )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'extension/xroweventmanager',
                                                                     'Start time is not valid.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            $startCheck = true;
        }

        if ( !( $endyear == '' and $endmonth == ''and $endday == '' and
                $endhour == '' and $endminute == '' ) )
        {
            $state = eZDateTimeValidator::validateDate( $endday, $endmonth, $endyear );
            if ( $state == eZInputValidator::STATE_INVALID )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'extension/xroweventmanager',
                                                                     'end date is not valid.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            $state = eZDateTimeValidator::validateTime( $endhour, $endminute );
            if ( $state == eZInputValidator::STATE_INVALID )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'extension/xroweventmanager',
                                                                     'end time is not valid.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            $endCheck = true;
        }

        if ( $maxparticipants != "" and $maxparticipants != 0 )
        {
            if ( !is_numeric( $maxparticipants ) )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'extension/xroweventmanager',
                                                                     'Enter a number at the max. member field.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            if ( $maxparticipants < 0 )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'extension/xroweventmanager',
                                                                     'The max. member number must not be lower than 0.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            $contentObjectID = $contentObjectAttribute->attribute( "contentobject_id" );
            $participantCount = xrowEventParticipants::countParticipants( $contentObjectID );
            if ( $participantCount > $maxparticipants and $status != xrowEvent::STATUS_EVENT_CANCELED )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'extension/xroweventmanager',
                                                                      "The number of participants who have already joined this event is higher than the number of the max. participants.
                                                                       Increase the maximum number or delete participants from the event." ) );
                return eZInputValidator::STATE_INVALID;
            }
            else if ( $participantCount == $maxparticipants and $status == xrowEvent::STATUS_PLACES_AVAILABLE )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'extension/xroweventmanager',
                                                                      "This event has already reached the maximum number of participants. Please change the status of the event." ) );
                return eZInputValidator::STATE_INVALID;
            }
        }

        if ( $status !== null )
        {
            if ( $status < 1 or $status > 3 )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'extension/xroweventmanager',
                                                                     "The event status isn't correct." ) );
                return eZInputValidator::STATE_INVALID;
            }
        }

        if ( $startCheck and $endCheck )
        {
            $startDateTime = new eZDateTime();
            $startDateTime->setMDYHMS( $startmonth, $startday, $startyear, $starthour, $startminute, 0 );
            $startTimeStamp = $startDateTime->timeStamp();

            $endDateTime = new eZDateTime();
            $endDateTime->setMDYHMS( $endmonth, $endday, $endyear, $endhour, $endminute, 0 );
            $endTimeStamp = $endDateTime->timeStamp();

            if ( $endTimeStamp <= $startTimeStamp )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'extension/xroweventmanager',
                                                                     "The event ends before it starts. Please correct the end date of the event." ) );
                return eZInputValidator::STATE_INVALID;
            }
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_xrowevent_start_year_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_start_month_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_start_day_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_start_hour_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_start_minute_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_end_year_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_end_month_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_end_day_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_end_hour_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_end_minute_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_max_participants_' . $contentObjectAttribute->attribute( 'id' ) )
        )
        {
            $startyear   = $http->postVariable( $base . '_xrowevent_start_year_' . $contentObjectAttribute->attribute( 'id' ) );
            $startmonth  = $http->postVariable( $base . '_xrowevent_start_month_' . $contentObjectAttribute->attribute( 'id' ) );
            $startday    = $http->postVariable( $base . '_xrowevent_start_day_' . $contentObjectAttribute->attribute( 'id' ) );
            $starthour   = $http->postVariable( $base . '_xrowevent_start_hour_' . $contentObjectAttribute->attribute( 'id' ) );
            $startminute = $http->postVariable( $base . '_xrowevent_start_minute_' . $contentObjectAttribute->attribute( 'id' ) );

            $endyear   = $http->postVariable( $base . '_xrowevent_end_year_' . $contentObjectAttribute->attribute( 'id' ) );
            $endmonth  = $http->postVariable( $base . '_xrowevent_end_month_' . $contentObjectAttribute->attribute( 'id' ) );
            $endday    = $http->postVariable( $base . '_xrowevent_end_day_' . $contentObjectAttribute->attribute( 'id' ) );
            $endhour   = $http->postVariable( $base . '_xrowevent_end_hour_' . $contentObjectAttribute->attribute( 'id' ) );
            $endminute = $http->postVariable( $base . '_xrowevent_end_minute_' . $contentObjectAttribute->attribute( 'id' ) );

            $maxparticipants = trim( $http->postVariable( $base . '_xrowevent_max_participants_' . $contentObjectAttribute->attribute( 'id' ) ) );
            if ( $maxparticipants == '' )
                $maxparticipants = 0;

            $status = null;
            if ( $http->hasPostVariable( $base . '_xrowevent_status_' . $contentObjectAttribute->attribute( 'id' ) ) )
                $status = $http->postVariable( $base . '_xrowevent_status_' . $contentObjectAttribute->attribute( 'id' ) );

            $comment = 0;
            if ( $http->hasPostVariable( $base . '_xrowevent_comment_' . $contentObjectAttribute->attribute( 'id' ) ) )
                $comment = 1;

            $classAttribute =& $contentObjectAttribute->contentClassAttribute();

            return $this->validateEventHTTPInput( $startday,
                                                  $startmonth,
                                                  $startyear,
                                                  $starthour,
                                                  $startminute,
                                                  $endday,
                                                  $endmonth,
                                                  $endyear,
                                                  $endhour,
                                                  $endminute,
                                                  $maxparticipants,
                                                  $status,
                                                  $comment,
                                                  $contentObjectAttribute );

        }
        else
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_xrowevent_start_year_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_start_month_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_start_day_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_start_hour_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_start_minute_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_end_year_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_end_month_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_end_day_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_end_hour_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_end_minute_' . $contentObjectAttribute->attribute( 'id' ) ) and
             $http->hasPostVariable( $base . '_xrowevent_max_participants_' . $contentObjectAttribute->attribute( 'id' ) )
        )
        {
            $startyear   = $http->postVariable( $base . '_xrowevent_start_year_' . $contentObjectAttribute->attribute( 'id' ) );
            $startmonth  = $http->postVariable( $base . '_xrowevent_start_month_' . $contentObjectAttribute->attribute( 'id' ) );
            $startday    = $http->postVariable( $base . '_xrowevent_start_day_' . $contentObjectAttribute->attribute( 'id' ) );
            $starthour   = $http->postVariable( $base . '_xrowevent_start_hour_' . $contentObjectAttribute->attribute( 'id' ) );
            $startminute = $http->postVariable( $base . '_xrowevent_start_minute_' . $contentObjectAttribute->attribute( 'id' ) );

            $endyear   = $http->postVariable( $base . '_xrowevent_end_year_' . $contentObjectAttribute->attribute( 'id' ) );
            $endmonth  = $http->postVariable( $base . '_xrowevent_end_month_' . $contentObjectAttribute->attribute( 'id' ) );
            $endday    = $http->postVariable( $base . '_xrowevent_end_day_' . $contentObjectAttribute->attribute( 'id' ) );
            $endhour   = $http->postVariable( $base . '_xrowevent_end_hour_' . $contentObjectAttribute->attribute( 'id' ) );
            $endminute = $http->postVariable( $base . '_xrowevent_end_minute_' . $contentObjectAttribute->attribute( 'id' ) );

            $maxParticipants = trim( $http->postVariable( $base . '_xrowevent_max_participants_' . $contentObjectAttribute->attribute( 'id' ) ) );
            if ( $maxParticipants == '' )
                $maxParticipants = 0;

            $data = array();

            $data['comment'] = 0;
            if ( $http->hasPostVariable( $base . '_xrowevent_comment_' . $contentObjectAttribute->attribute( 'id' ) ) )
                $data['comment'] = 1;

            if ( $http->hasPostVariable( $base . '_xrowevent_status_' . $contentObjectAttribute->attribute( 'id' ) ) )
                $data['status'] = $http->postVariable( $base . '_xrowevent_status_' . $contentObjectAttribute->attribute( 'id' ) );
            else
                $data['status'] = xrowEvent::STATUS_PLACES_AVAILABLE;

            if ( ( $startyear == '' and $startmonth == ''and $startday == '' and
                   $starthour == '' and $startminute == '' ) or
                   !checkdate( $startmonth, $startday, $startyear ) or $startyear < 1970 )
            {
                $data['start_date'] = 0;
            }
            else
            {
                $startDateTime = new eZDateTime();
                $startDateTime->setMDYHMS( $startmonth, $startday, $startyear, $starthour, $startminute, 0 );
                $data['start_date'] = $startDateTime->timeStamp();
            }

            if ( ( $endyear == '' and $endmonth == ''and $endday == '' and
                   $endhour == '' and $endminute == '' ) or
                   !checkdate( $endmonth, $endday, $endyear ) or $endyear < 1970 )
            {
                $data['end_date'] = 0;
            }
            else
            {
                $endDateTime = new eZDateTime();
                $endDateTime->setMDYHMS( $endmonth, $endday, $endyear, $endhour, $endminute, 0 );
                $data['end_date'] = $endDateTime->timeStamp();
            }

            $data['max_participants'] = $maxParticipants;

            $data['contentobject_id'] = $contentObjectAttribute->attribute( 'contentobject_id' );

            $event = xrowEvent::saveEvent( $data );

            // to enable an easy content fetch of events which are in the future
            $contentObjectAttribute->setAttribute( 'data_int', $data['start_date'] );
            $contentObjectAttribute->store();

            return true;
        }
        return false;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $eventID = $contentObjectAttribute->attribute( 'contentobject_id' );
        $event = xrowEvent::fetch( $eventID );

        if ( is_object( $event ) )
        {
            if ( $event->attribute( 'start_date' ) > 0 )
                return true;
            else
                return false;
        }
        else
            return false;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        if ( isset( $GLOBALS['xrowEventManagerCache'][$contentObjectAttribute->ContentObjectID][$contentObjectAttribute->Version] ) )
            return $GLOBALS['xrowEventManagerCache'][$contentObjectAttribute->ContentObjectID][$contentObjectAttribute->Version];
        else
        {
            $eventID = $contentObjectAttribute->attribute( 'contentobject_id' );
            $eventObj = xrowEvent::fetch( $eventID );

            if ( !is_object( $eventObj ) )
                $eventObj = new xrowEvent( array( 'contentobject_id' => $eventID,
                                                  'start_date' => 0,
                                                  'end_date' => 0,
                                                  'status' => xrowEvent::STATUS_DRAFT,
                                                  'max_participants' => 0 ) );
            $GLOBALS['xrowEventManagerCache'][$contentObjectAttribute->ContentObjectID][$contentObjectAttribute->Version] = $eventObj;
            return $eventObj;
        }
    }

    function storeObjectAttribute( $contentObjectAttribute )
    {
        if ( isset( $GLOBALS['xrowEventManagerCache'][$contentObjectAttribute->ContentObjectID][$contentObjectAttribute->Version] ) )
            unset( $GLOBALS['xrowEventManagerCache'][$contentObjectAttribute->ContentObjectID][$contentObjectAttribute->Version] );
    }

    /*!
     \reimp
    */
    function isIndexable()
    {
        return false;
    }

    /*!
     \reimp
    */
    function isInformationCollector()
    {
        return false;
    }

    /*!
     \reimp
    */
    function title( $contentObjectAttribute, $name = 'original_filename' )
    {
        $eventObj = $contentObjectAttribute->content();
        if ( is_object( $eventObj ) )
        {
            $locale =& eZLocale::instance();
            $value = $locale->formatDateTime( $eventObj->attribute( "start_date" ) ) . ' - ' . $locale->formatDateTime( $eventObj->attribute( "end_date" ) );
            return $value;
        }
        return "";
    }

    /*!
     \reimp
    */
    function metaData( $contentObjectAttribute )
    {
        $eventObj = $contentObjectAttribute->content();
        $value = "";
        if ( is_object( $eventObj ) )
        {
            $locale =& eZLocale::instance();
            $value = $locale->formatDateTime( $eventObj->attribute( "start_date" ) ) . ' - ' . $locale->formatDateTime( $eventObj->attribute( "end_date" ) );
            return $value;
        }
        return "";
    }

    /*!
     \reimp
    */
    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        switch ( $action )
        {
            case 'add_me':
            {
                $userID = eZUser::currentUserID();
                $eventObj = $contentObjectAttribute->content();
                if ( is_object( $eventObj ) )
                    $eventObj->addPerson( $userID );
                $this->storeObjectAttribute( $contentObjectAttribute );

            }break;

            case 'add_persons':
            {
                $module =& $parameters['module'];
                $redirectionURI = $parameters['current-redirection-uri'];

                include_once( 'kernel/classes/ezcontentbrowse.php' );
                $browseParameters = array( 'action_name' => 'BrowsePersonList',
                                           'description_template' => 'design:content/browse_add_persons.tpl',
                                           'persistent_data' => array( 'object_id' => $contentObjectAttribute->attribute( 'contentobject_id' ), 'version_id' => $contentObjectAttribute->attribute( 'version' ) ),
                                           'browse_custom_action' => array( 'name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) . '_set_person_list]',
                                                                            'value' => $contentObjectAttribute->attribute( 'id' ) ),
                                           'from_page' => $redirectionURI );
                eZContentBrowse::browse( $browseParameters,
                                         $module );

            }break;

            case 'set_person_list':
            {
                if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
                {
                    $selectedObjectIDArray = $http->postVariable( "SelectedObjectIDArray" );
                    $eventObj = $contentObjectAttribute->content();
                    if ( is_object( $eventObj ) )
                    {
                        $personArray = $eventObj->attribute( 'persons' );
                        $personIDArray = array();

                        foreach( $personArray as $key => $item )
                        {
                            $personIDArray[] = $item->attribute( 'user_id' );
                        }

                        foreach ( $selectedObjectIDArray as $objectID )
                        {
                            // Check if the given object ID has a numeric value, if not go to the next object.
                            if ( !is_numeric( $objectID ) )
                            {
                                eZDebug::writeError( "Related object ID (objectID): '$objectID', is not a numeric value.",
                                    "xrowEventType::customObjectAttributeHTTPAction" );

                                continue;
                            }

                           if ( !in_array( $objectID, $personIDArray ) )
                                $eventObj->addPerson( $objectID );
                        }
                        $this->storeObjectAttribute( $contentObjectAttribute );
                    }
                }

            }break;

            case 'remove_persons':
            {
                if ( $http->hasPostVariable( 'xrowevent_person_array_' . $contentObjectAttribute->attribute( 'id' ) ) )
                {
                    $personArray = $http->postVariable( 'xrowevent_person_array_' . $contentObjectAttribute->attribute( 'id' ) );

                    $eventObj = $contentObjectAttribute->content();

                    foreach( $personArray as $id => $person )
                    {
                        $eventObj->removePerson( $person );
                    }

                    $this->storeObjectAttribute( $contentObjectAttribute );
                }

            }break;

            default:
            {
                 eZDebug::writeError( "Unknown custom HTTP action: " . $action,
                                       'xrowEventType' );
            }break;
        }
    }

    /*!
     \reimp
    */
    function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
        $db =& eZDB::instance();
        $contentObjectID = $contentObjectAttribute->attribute( "contentobject_id" );

        $res = $db->arrayQuery( "SELECT COUNT(*) AS version_count FROM ezcontentobject_version WHERE contentobject_id = $contentObjectID" );
        $versionCount = $res[0]['version_count'];

        if ( $version == null || $versionCount <= 1 )
        {
            xrowEvent::removeEvent( $contentObjectID );
        }
    }

    /*!
     \reimp
    */
    function sortKey( $contentObjectAttribute )
    {
        return (int)$contentObjectAttribute->attribute( 'data_int' );
    }

    /*!
     \reimp
    */
    function sortKeyType()
    {
        return 'int';
    }
}

eZDataType::register( xrowEventType::DATA_TYPE_STRING, "xrowEventType" );
?>