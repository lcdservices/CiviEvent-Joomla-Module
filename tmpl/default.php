<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
?>

<link rel="stylesheet" href="modules/mod_civievent/civievent.css" type="text/css" />

<?php 

echo '<ul class="civieventlist">';

// Set number of records to be displayed and clear counter
$maxevents = 10;
$maxevents = $display_params[maxevents];
//echo $maxevents."<br />";
$x = 1;

foreach ($eventtitles as &$event) {	

	if ($x > $maxevents) {
		return;
	} else {
		//echo $x; //display count
		$x++;
	}   
    
	for ($i=0, $n=count( $event->title ); ($i < $n) ; $i++) {
    	
    	if($display_params[link]==1) { //link set to register
    		$link 		= JRoute::_( 'index.php?option=com_civicrm&task=civicrm/event/register&reset=1&id='. $event->eventID );
    		$modallink 	= JRoute::_( 'index.php?option=com_civicrm&task=civicrm/event/register&reset=1&id='. $event->eventID.'&tmpl=component' );
    	} else { //link set to info page
    		$link 		= JRoute::_( 'index.php?option=com_civicrm&task=civicrm/event/info&reset=1&id='. $event->eventID );
    		$modallink 	= JRoute::_( 'index.php?option=com_civicrm&task=civicrm/event/info&reset=1&id='. $event->eventID.'&tmpl=component' );
			$registernow = JRoute::_( 'index.php?option=com_civicrm&task=civicrm/event/register&reset=1&id='. $event->eventID );
		}
		
		// Format date
		$event->start_date = JHTML::_('date', $event->start_date, $display_params[dateformat], 0); //LCD param 0?
		if ($event->end_date) {
			$event->end_date = JHTML::_('date', $event->end_date, $display_params[dateformat], 0);
		}
		
		if ($event->end_date) {
			$eventdate = $event->start_date." &#8211;".$event->end_date;
		} else {
			$eventdate = $event->start_date;
		}
		
		// Build html
		if($display_params[modal]) {
			$linkhtml = '<li><a href="'.$link.'" class="class_name">'.$event->title.'</a>';
		} else {
			$linkhtml = '<li><a class="modal" href="'.$modallink.'" rel="{handler: \'iframe\', size: {x: 520, y: 400}}">'.$event->title.'</a>';
		}
		echo $linkhtml;
		if ($display_params[link]==2) {
			echo '<br /><span class="eventregisternow">&raquo; <a href="'.$registernow.'">Register Now</a></span>';
		}
		if ($display_params[showdates]) {
			echo '<br /><span class="eventdate">'.$eventdate.'</span>';
		}
		if ($display_params[summary]==0) {
			echo '<br /><span class="eventsummary">'.$event->summary.'</span>';
		}
		echo '</li>';

    }
}

echo '</ul>';
?>
    	
