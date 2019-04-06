<?php 
/**
* CiviEvent Joomla Module
*
* @copyright    Copyright (C) 2005-2011 Open Source Matters. All rights reserved.
* @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.html');

if ($displayParams['modal']) {
  jimport('joomla.html.html.behavior');
  JHtmlBehavior::modal();
}

if ($params->get('includecss',0)) {
  $cssdoc = JFactory::getDocument()->addStyleSheet("modules/mod_civievent/civievent.css");
}

if (count($eventtitles)) {
  echo '<ul class="civieventlist">';
  
  // Set number of records to be displayed and clear counter
  $maxevents = ($displayParams['maxevents']) ? $displayParams['maxevents'] : 10;
  
  $x = 1;
  foreach ($eventtitles as $event) {
  	if ($x > $maxevents) {
  		return;
  	}
    else {
  		$x++;
  	}   
      
    $baselink = 'index.php?option=com_civicrm&task=civicrm/event/';

    if($displayParams['link'] == 1) { //link set to register
      $link = $baselink.'register&reset=1&id='.$event['eventID'];
      $modallink = $baselink.'register&reset=1&id='.$event['eventID'].'&tmpl=component';
    }
    else { //link set to info page
      $link = $baselink.'info&reset=1&id='.$event['eventID'];
      $modallink = $baselink.'info&reset=1&id='.$event['eventID'].'&tmpl=component';
      $registernow = $baselink.'register&reset=1&id='.$event['eventID'];
    }

    if ($displayParams['itemid']) {
      $link .= '&Itemid='.$displayParams['itemid'];
      $modallink .= '&Itemid='.$displayParams['itemid'];

      if($registernow) {
        $registernow .= '&Itemid='.$displayParams['itemid'];
      }
    }

    //run the URLs through the router
    $link = JRoute::_($link);
    $modallink = JRoute::_($modallink);
    $registernow = ($registernow) ? JRoute::_($registernow) : '';

    // Format date
    $event['start_date'] = ($displayParams['datezone']) ?
      JHtml::date($event['start_date'], $displayParams['dateformat']) :
      date($displayParams['dateformat'], strtotime($event['start_date']))
    ;

    if ($event['end_date']) {
      $event['end_date'] = ($displayParams['datezone']) ?
        JHtml::date($event['end_date'], $displayParams['dateformat']) :
        date($displayParams['dateformat'], strtotime($event['end_date']))
      ;
    }

    if ($event['end_date'] && $displayParams['showdates'] != 2) {
      $eventdate = $event['start_date']." &#8211;".$event['end_date'];
    }
    else {
      $eventdate = $event['start_date'];
    }

    // Build html
    // The title with link
    $thehtml = [
      'titlelink' => '',
      'registerlink' => '',
      'dates' => '',
      'summary' => ''
    ];

    if( $displayParams['modal'] ) {
      $linkhtml = '<a class="modal civieventlist-item-title-link" href="'.$modallink.
        '" rel="{handler: \'iframe\', size: {x: 520, y: 400}}">'.$event['title'].'</a>';
    }
    else {
      $linkhtml = '<a href="'.$link.'" class="civieventlist-item-title-link">'.$event['title'].'</a>';
    }
    $thehtml['titlelink'] = "<div class='civieventlist-item-title'>$linkhtml</div>";

    // The register link, if included
    if ($displayParams['link'] == 2) {
      $thehtml['registerlink'] =
        "<div class='civieventlist-item-register'>" .
        "<span class='eventregisternow'>&raquo; <a href='$registernow'>Register Now</a></span>" .
        "</div>";
    }

    // The date(s) of the event
    if ($displayParams['showdates']) {
      $thehtml['dates'] = "<div class='civieventlist-item-date'><span class='eventdate'>$eventdate</span></div>";
    }

    // The summary text, if included
    if ($displayParams['summary'] == 1 && $event['summary']) {
      $thehtml['summary'] = "<div class='civieventlist-item-summary'><span class='eventsummary'>{$event['summary']}</span></div>";
    }

    if ($displayParams['includecity'] == 1 && $event['city']){
      $thehtml['city'] = "<div class='civieventlist-item-city'><span class='eventcity'>{$event['city']}</span></div>";
    }

    // Put it all together
    $fullhtml = "<li class='civieventlist-item'>" .
      CRM_Utils_Array::value('dates', $thehtml) .
      CRM_Utils_Array::value('titlelink', $thehtml) .
      CRM_Utils_Array::value('registerlink', $thehtml) .
      CRM_Utils_Array::value('summary', $thehtml) .
      CRM_Utils_Array::value('city', $thehtml) .
      '</li>';
    echo $fullhtml;
  }
  
  echo '</ul>';
}
else {
  if ($params->get('noeventtext', '')) {
    echo '<div class="civieventlist-no-events">'.htmlspecialchars($params->get('noeventtext', '')).'</div>';
  }
}
