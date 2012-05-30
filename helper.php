<?php

class modCiviEventHelper
{

	// show online member names
	function getEventTitles(&$params) {
		jimport( 'joomla.application.module.helper' );
		
		$mode		= trim($params->get('mode'));
		$db			=& JFactory::getDBO();
		$result		= null;
		$link		= trim($params->get('link'));
		$multievent	= $params->get('multievent');
		$tid  		= trim($params->get('tid'));
		$startdate	= trim($params->get('startdate'));
		$enddate	= trim($params->get('enddate'));
		$privacy	= trim($params->get('privacy'));
		$enddate	= trim($params->get('enddate'));
		$sort		= trim($params->get('sort'));
		$sortop		= trim($params->get('sortop'));
		$isactive	= " AND is_active = 1 ";
		$isonline   = " AND is_online_registration = 1 ";
	
		//retrieve all custom data tables impacting events
		$customdatapresent = 1; //assume data unless none found below
		
		$query_customevent = " SELECT table_name FROM civicrm_custom_group WHERE extends = 'Event' ";
			
		$db->setQuery($query_customevent);
		$result_customevent = $db->loadObjectList();

		if ($db->getErrorNum()) {
			$customdatapresent = 0;
		}
		//print_r($result_customevent);
		//echo '<br />';

		//set core SELECT and FROM clauses based on presence of custom fields
		if ( $customdatapresent == 0 ) { //no custom data
			$select = 'SELECT title, id AS eventID, start_date, end_date, event_type_id, summary, is_active, is_public, is_online_registration';
			$from = 'FROM civicrm_event';
			
		} elseif ( $customdatapresent == 1 ) { //custom data present
		
			//for each custom event custom data table, build SELECT and FROM clause
			//$arraycount = 0;
			$select = 'SELECT civicrm_event.id AS eventID, civicrm_event.*';
			$from = 'FROM civicrm_event';
			
			foreach( $result_customevent as $key => $value){ //recurse through the custom tables and build sql
				foreach($value as $tablename) {
					$select .= ', '.$tablename.'.*';
					$from .= ' LEFT OUTER JOIN '.$tablename.' ON ('.$tablename.'.entity_id = civicrm_event.id)';
				}
				$arraycount = $arraycount + 1;
			}
			//echo $select.'<br />';
			//echo $from.'<br />';
			//echo $arraycount.'<br />';
		}
		
		//set core WHERE clause
		$where = 'WHERE is_active = 1 AND is_template != 1 ';
		
		//set default date range to current and future events only. this is overwritten if date select mode is used
		$currentdate = date("Y-m-d");
			//only view events that end on or after current date, or where no end_date is defined
			$wheredaterange = " AND ( end_date >= '".$currentdate."' OR end_date IS NULL OR end_date = '' ) "; 
			//only view events that start on or after current date	
			$wheredaterange .= " AND start_date >= '".$currentdate."'"; 
		
		//determine privacy setting
		switch($privacy)
		{
		case(0):
				{
					$privacy = "";
					break;
				}
		case(1):
				{
					$privacy = " AND is_public = 1 ";
					break;
				}
		case(2):
				{
					$privacy =" AND is_public = 0 ";
					break;
				}
		}
		$where .= $privacy; //add privacy to WHERE clause
		
		//determine sort order
		switch($sort)
		{
		case(0):
				{
					$sort = " ORDER BY title ".$sortop;
					break;
				}
		case(1):
				{
					$sort = " ORDER BY  start_date ".$sortop;
					break;
				}
		case(2):
				{
					$sort =" ORDER BY  end_date ".$sortop;
					break;
				}
		}
		
		//determine link type (info or reg). if reg, make sure events have online reg setup
		if($link == "1"){
			$where .= $isonline;
		}
		
//CONFIRMED
		
		//determine display mode, affect where clause where appropriate
		switch($mode)
		{	
		case(0): //default mode
			{
			break;
		}
		
		case(1): //date range mode
			{
			//Rewrite date format
			if ($startdate) $startdate = date('Y-m-d', strtotime($startdate));
			if ($enddate) {
				//Add 1 day to end date to fix date range criteria
				$enddate = strtotime ( '+1 day' , strtotime($enddate) );
				$enddate = date('Y-m-d', $enddate);
			}
			
			if( $startdate=="" OR $startdate=="Select" ) //no start date selected, throw error
				{
				JError::raiseWarning(500,"No start date selected for CiviEvent module.");
				}
			elseif( $enddate=="" OR $enddate=="Select" ) //Open end date parameter
				{
				$wheredaterange = " AND start_date >= '".$startdate."'";
				}
			else //both start and end date parameter, replace default end date range
				{
				//$wheredaterange = " AND start_date >= '".$startdate."'"." AND end_date <= '".$enddate."'";
				
				//v2 BOTH start/end date measured by event start date in order to make month-wrap ranges ruled by start
				$wheredaterange = " AND start_date >= '".$startdate."'"." AND start_date < '".$enddate."'";
				}
			break;
		}
		
		case(2): //custom select mode
		{	
			$where .= ( (count( $multievent )>1) ? ' AND ( id='. implode( ' OR id= ', $multievent ).' ) ' : ' AND id='.$multievent );
			break;
		}	
				
		case(3): //event type mode
		{
			$where .= ' AND event_type_id = '.$tid;
			break;
		}
	} //close mode switch
		
		//build query statement
		$query = $select.' ';
		$query .= $from.' ';
		$query .= $where.$wheredaterange.' ';
		$query .= $sort;
		
		//run $query;
		$db->setQuery($query);
		$result = $db->loadObjectList();
		

		if ($db->getErrorNum()) {
			JError::raiseWarning(500,"No events meet the selected criteria.");
		}

		return $result;
		
	} //end getEventTitles
	
	function sendParam(&$params) {
		$displayParams['link']       = trim($params->get('link'));
		$displayParams['modal']      = trim($params->get('modal'));
		$displayParams['maxevents']  = trim($params->get('maxevents'));
		$displayParams['showdates']  = trim($params->get('showdates'));
		$displayParams['dateformat'] = trim($params->get('dateformat'));
		$displayParams['summary']    = trim($params->get('summary'));
		$displayParams['itemid']     = trim($params->get('itemid'));
	
		return $displayParams;
	} //end sendParams
}

?>