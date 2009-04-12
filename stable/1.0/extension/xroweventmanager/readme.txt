xrow EventManager, 1.0
---------------------------------------------
Contains a datatype to manage a start end enddate of an event, the max. amount of participants, the event status and persons who are responsible for the event.

Installation:
---------------------------------------------
-) Install tables, look at sql/xroweventmanager.sql
-) Activate extension
-) Clear caches
-) Add the xrowevent datatype to a content class
-) There are three policies for using this module:
   -) use: Users are allowed to use this module, they can e.g. join events
   -) manage: Users are allowed to manage the events
   -) administrate: admin rights, see all events
-) Don't cache pages which contain join / subscription buttons (people are not allowed to join events which are in the past)

Restrictions:
---------------------------------------------
-) No version control of the event data
-) No translations possible

xrow EventManager, 1.1
---------------------------------------------
-) Participants can leave a comment before the join an event (table update required, see sql/xroweventmanager.sql)
-) Added left menu
-) Addes example event manager class in packages/event_manager-1.0-1.ezpkg