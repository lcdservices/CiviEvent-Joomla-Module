<?php

class modCiviEventHelper
{

  // show online member names
  static function getEventTitles(&$params) {
    jimport( 'joomla.application.module.helper' );
    
    $mode = trim($params->get('mode'));
    $result = null;
    $link = trim($params->get('link'));
    $multievent = $params->get('multievent');
    $tid = $params->get('tid');
    $startdate = trim($params->get('startdate'));
    $privacy = trim($params->get('privacy'));
    $enddate = trim($params->get('enddate'));
    $sort = trim($params->get('sort'));
    $sortop = trim($params->get('sortop'));
    $isonline = " AND is_online_registration = 1 ";

    //initialize CiviCRM
    require_once JPATH_ROOT.'/administrator/components/com_civicrm/civicrm.settings.php';
    require_once 'CRM/Core/Config.php';
    $config = CRM_Core_Config::singleton();
    
    $sql = "
      SELECT table_name
      FROM civicrm_custom_group
      WHERE extends = 'Event'
        AND is_active = 1
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    //Civi::log()->debug('getEventTitles', array('$dao1' => $dao));

    //set core SELECT and FROM clauses based on presence of custom fields
    if ($dao->N == 0) {
      $select = '
        SELECT title, 
          civicrm_event.id AS eventID, 
          start_date, 
          end_date, 
          event_type_id, 
          summary, 
          is_active, 
          is_public, 
          is_online_registration
      ';
      $from = 'FROM civicrm_event';
    }
    else { //custom data present
      //for each custom event custom data table, build SELECT and FROM clause
      $select = '
        SELECT civicrm_event.id AS eventID, civicrm_event.*
      ';
      $from = 'FROM civicrm_event';
      $arraycount = 0;
      
      while($dao->fetch()){
        //recurse through the custom tables and build sql
        $select .= ', '.$dao->table_name.'.*';
        $from .= '
          LEFT OUTER JOIN '.$dao->table_name.' 
            ON '.$dao->table_name.'.entity_id = civicrm_event.id
        ';
        $arraycount ++;
      }
    }

    if($params->get('includecity',0)){
      $select .= ', ca.city';
      $from .= '
          LEFT OUTER JOIN civicrm_loc_block clb 
            ON clb.id = civicrm_event.loc_block_id
          LEFT OUTER JOIN civicrm_address ca
            ON ca.id = clb.address_id
        ';
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
    switch($privacy) {
      case(0):
        $privacy = "";
        break;
      case(1):
        $privacy = " AND is_public = 1 ";
        break;
      case(2):
        $privacy =" AND is_public = 0 ";
        break;
    }
    $where .= $privacy; //add privacy to WHERE clause
    
    //determine sort order
    switch ($sort) {
    case(0):
      $sort = " ORDER BY title {$sortop} ";
      break;
    case(1):
      $sort = " ORDER BY start_date {$sortop}, end_date {$sortop} ";
      break;
    case(2):
      $sort = " ORDER BY end_date {$sortop}, start_date {$sortop} ";
      break;
    }
    
    //determine link type (info or reg). if reg, make sure events have online reg setup
    if($link == "1"){
      $where .= $isonline;
    }

    //determine display mode, affect where clause where appropriate
    switch ($mode) {
    case(0): //default mode
      break;
    
    case(1): //date range mode
      //Rewrite date format
      if ($startdate) $startdate = date('Y-m-d', strtotime($startdate));
      if ($enddate) {
        //Add 1 day to end date to fix date range criteria
        $enddate = strtotime ( '+1 day' , strtotime($enddate) );
        $enddate = date('Y-m-d', $enddate);
      }
      
      if ( $startdate == "" || $startdate == "Select" ) {
        //no start date selected, throw error
        JError::raiseWarning(500,"No start date selected for CiviEvent module.");
      }
      elseif ( $enddate == "" || $enddate == "Select" ) {
        //Open end date parameter
        $wheredaterange = " AND start_date >= '".$startdate."'";
      }
      else {
        //BOTH start/end date measured by event start date in order to make month-wrap ranges ruled by start
        $wheredaterange = " AND start_date >= '".$startdate."'"." AND start_date < '".$enddate."'";
      }
      break;
    
    case(2): //custom select mode
      $where .= ( (count( $multievent )>1) ? ' AND ( id='. implode( ' OR id= ', $multievent ).' ) ' : ' AND id='.$multievent );
      break;
        
    case(3): //event type mode
      if ( is_array($tid) ) {
        $tidList = implode(',', $tid);
        $where .= " AND event_type_id IN ({$tidList}) ";
      }
      else {
        $where .= " AND event_type_id = $tid ";
      }
      break;
  } //close mode switch
    
    //build query statement
    $query = $select.' ';
    $query .= $from.' ';
    $query .= $where.$wheredaterange.' ';
    $query .= $sort;
    
    //run $query;
    $dao = CRM_Core_DAO::executeQuery($query);
    $result = $dao->fetchAll();
    //Civi::log()->debug('getEventTitles', array('result' => $result));

    if ($dao->N == 0) {
      JError::raiseWarning(500, "No events meet the selected criteria.");
    }

		return $result;
		
	} //end getEventTitles
  
  static function sendParam(&$params) {
    $displayParams = array(
      'link' => trim($params->get('link')),
      'modal' => trim($params->get('modal')),
      'maxevents' => trim($params->get('maxevents')),
      'showdates' => trim($params->get('showdates')),
      'includecity' => trim($params->get('includecity')),
      'dateformat' => trim($params->get('dateformat')),
      'datezone' => trim($params->get('datezone')),
      'summary' => trim($params->get('summary')),
      'itemid' => trim($params->get('itemid')),
    );
  
    return $displayParams;
  } //end sendParams
}
