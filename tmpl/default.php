<?php 
/**
* CiviEvent Joomla Module
*
* @copyright    Copyright (C) 2005-2011 Open Source Matters. All rights reserved.
* @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// No direct access
defined('_JEXEC') or die;

jimport( 'joomla.html.html' );

if ( $displayParams['modal'] ) {
    jimport( 'joomla.html.html.behavior' );
    JHtmlBehavior::modal();
}
?>

<link rel="stylesheet" href="modules/mod_civievent/civievent.css" type="text/css" />

<?php 
echo '<ul class="civieventlist">';

// Set number of records to be displayed and clear counter
$maxevents = ( $displayParams['maxevents'] ) ? $displayParams['maxevents'] : 10;

$x = 1;

foreach ($eventtitles as &$event) {	

	if ($x > $maxevents) {
		return;
	} else {
		$x++;
	}   
    
    $baselink = 'index.php?option=com_civicrm&task=civicrm/event/';
    
	for ($i=0, $n=count( $event->title ); ($i < $n) ; $i++) {
    	if($displayParams['link']==1) { //link set to register
    		$link 		 = JRoute::_( $baselink.'register&reset=1&id='.$event->eventID );
    		$modallink 	 = JRoute::_( $baselink.'register&reset=1&id='.$event->eventID.'&tmpl=component' );
    	} else { //link set to info page
    		$link 		 = JRoute::_( $baselink.'info&reset=1&id='.$event->eventID );
    		$modallink 	 = JRoute::_( $baselink.'info&reset=1&id='.$event->eventID.'&tmpl=component' );
			$registernow = JRoute::_( $baselink.'register&reset=1&id='.$event->eventID );
		}
		
		if ($displayParams['itemid']) {
            $link .= '&Itemid='.$displayParams['itemid'];
            $modallink .= '&Itemid='.$displayParams['itemid'];
            
            if($registernow) {
                $registernow .= '&Itemid='.$displayParams['itemid'];
            }
		}
		
		// Format date
		$event->start_date = JHtml::date( $event->start_date, $displayParams['dateformat'] );
		if ($event->end_date) {
			$event->end_date = JHtml::date( $event->end_date, $displayParams['dateformat'] );
		}
		
		if ($event->end_date) {
			$eventdate = $event->start_date." &#8211;".$event->end_date;
		} else {
			$eventdate = $event->start_date;
		}
		
		// Build html
		if( $displayParams['modal'] ) {
			$linkhtml = '<li><a class="modal" href="'.$modallink.'" rel="{handler: \'iframe\', size: {x: 520, y: 400}}">'.$event->title.'</a>';
		} else {
			$linkhtml = '<li><a href="'.$link.'" class="class_name">'.$event->title.'</a>';
		}
		echo $linkhtml;
		
		if ( $displayParams['link']==2 ) {
			echo '<br /><span class="eventregisternow">&raquo; <a href="'.$registernow.'">Register Now</a></span>';
		}
		if ( $displayParams['showdates'] ) {
			echo '<br /><span class="eventdate">'.$eventdate.'</span>';
		}
		if ( $displayParams['summary'] ) {
			echo '<br /><span class="eventsummary">'.$event->summary.'</span>';
		}
		echo '</li>';

    }
}

echo '</ul>';
?>
    	
